<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalTemplate;
use App\Support\ProposalPdfRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AdminProposalController extends Controller
{
    private const STATUSES = ['draft', 'sent', 'viewed', 'accepted', 'rejected'];

    public function index(): View
    {
        $this->syncDefaultTemplates();

        return $this->builderView();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProposalRequest($request);
        $template = ProposalTemplate::query()->findOrFail($validated['proposal_template_id']);
        [$pricingItems, $totals] = $this->normalizePricingItems(
            $this->decodePayload($request->input('pricing_payload'), config('proposals.pricing_items', []))
        );

        $proposal = DB::transaction(function () use ($request, $template, $validated, $pricingItems, $totals): Proposal {
            $proposal = Proposal::query()->create($this->proposalAttributes($validated, $template, $totals));

            $this->storeUploadedAsset($proposal, $request, 'logo', 'logo', 'logo_path');
            $this->storeUploadedAsset($proposal, $request, 'cover_image', 'cover', 'cover_image_path');
            $this->storeUploadedAsset($proposal, $request, 'background_image', 'background', 'background_image_path');
            $this->syncProposalChildren($proposal, $request, $pricingItems);

            return $proposal->fresh();
        });

        return redirect()
            ->route('admin.proposals.show', $proposal)
            ->with('status', "Proposal {$proposal->proposal_number} created.");
    }

    public function show(Proposal $proposal): View
    {
        return view('admin.proposals.show', [
            'proposal' => $this->loadProposalDocument($proposal),
            'shareUrl' => route('proposals.share', $proposal->public_token),
        ]);
    }

    public function edit(Proposal $proposal): View
    {
        $this->syncDefaultTemplates();

        return $this->builderView($this->loadProposalDocument($proposal));
    }

    public function update(Request $request, Proposal $proposal): RedirectResponse
    {
        $validated = $this->validateProposalRequest($request);
        $template = ProposalTemplate::query()->findOrFail($validated['proposal_template_id']);
        [$pricingItems, $totals] = $this->normalizePricingItems(
            $this->decodePayload($request->input('pricing_payload'), config('proposals.pricing_items', []))
        );

        DB::transaction(function () use ($proposal, $request, $template, $validated, $pricingItems, $totals): void {
            $proposal->update($this->proposalAttributes($validated, $template, $totals, $proposal));

            $this->storeUploadedAsset($proposal, $request, 'logo', 'logo', 'logo_path');
            $this->storeUploadedAsset($proposal, $request, 'cover_image', 'cover', 'cover_image_path');
            $this->storeUploadedAsset($proposal, $request, 'background_image', 'background', 'background_image_path');
            $this->syncProposalChildren($proposal, $request, $pricingItems);
        });

        return redirect()
            ->route('admin.proposals.show', $proposal)
            ->with('status', "Proposal {$proposal->proposal_number} updated.");
    }

    public function duplicate(Proposal $proposal): RedirectResponse
    {
        $proposal = $this->loadProposalDocument($proposal);

        $copy = DB::transaction(function () use ($proposal): Proposal {
            $copy = $proposal->replicate([
                'proposal_number',
                'public_token',
                'status',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
            $copy->proposal_number = $this->generateProposalNumber();
            $copy->public_token = Str::random(48);
            $copy->status = 'draft';
            $copy->title = $proposal->title.' Copy';
            $copy->reference_number = $this->generateReferenceNumber();
            $copy->save();

            if ($proposal->settings) {
                $copy->settings()->create($proposal->settings->only([
                    'primary_color',
                    'secondary_color',
                    'accent_color',
                    'font_family',
                    'header_style',
                    'footer_style',
                    'page_numbering',
                    'watermark',
                    'options',
                ]));
            }

            foreach ($proposal->sections as $section) {
                $copy->sections()->create($section->only([
                    'type',
                    'title',
                    'eyebrow',
                    'body',
                    'payload',
                    'layout_style',
                    'is_visible',
                    'sort_order',
                ]));
            }

            foreach ($proposal->pricingItems as $item) {
                $copy->pricingItems()->create($item->only([
                    'package',
                    'service_name',
                    'description',
                    'quantity',
                    'unit_price',
                    'discount',
                    'tax_rate',
                    'line_total',
                    'sort_order',
                ]));
            }

            foreach ($proposal->timelines as $timeline) {
                $copy->timelines()->create($timeline->only([
                    'phase_title',
                    'description',
                    'start_date',
                    'end_date',
                    'duration',
                    'deliverables',
                    'status',
                    'sort_order',
                ]));
            }

            foreach ($proposal->teamMembers as $member) {
                $copy->teamMembers()->create($member->only([
                    'name',
                    'role',
                    'bio',
                    'profile_image_path',
                    'email',
                    'social_link',
                    'sort_order',
                ]));
            }

            return $copy;
        });

        return redirect()
            ->route('admin.proposals.edit', $copy)
            ->with('status', "Proposal {$proposal->proposal_number} duplicated as {$copy->proposal_number}.");
    }

    public function destroy(Proposal $proposal): RedirectResponse
    {
        $proposal->delete();

        return redirect()
            ->route('admin.proposals.index')
            ->with('status', 'Proposal moved out of the active dashboard.');
    }

    public function downloadPdf(Proposal $proposal, ProposalPdfRenderer $renderer): Response
    {
        $proposal = $this->loadProposalDocument($proposal);
        $fileName = Str::slug($proposal->title.' '.$proposal->proposal_number).'.pdf';
        $pdf = $renderer->render($proposal);

        $proposal->exports()->create([
            'format' => 'pdf',
            'file_name' => $fileName,
            'exported_by' => session('luxury_quote_admin_email'),
            'metadata' => ['status' => $proposal->status],
        ]);

        return response($pdf, 200, [
            'Cache-Control' => 'no-store, max-age=0',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Content-Length' => (string) strlen($pdf),
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function downloadWord(Proposal $proposal): Response
    {
        $proposal = $this->loadProposalDocument($proposal);
        $fileName = Str::slug($proposal->title.' '.$proposal->proposal_number).'.doc';

        $proposal->exports()->create([
            'format' => 'word',
            'file_name' => $fileName,
            'exported_by' => session('luxury_quote_admin_email'),
            'metadata' => ['status' => $proposal->status],
        ]);

        return response(view('admin.proposals.word', [
            'proposal' => $proposal,
        ])->render(), 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    public function print(Proposal $proposal): View
    {
        return view('admin.proposals.print', [
            'proposal' => $this->loadProposalDocument($proposal),
        ]);
    }

    public function share(string $token): View
    {
        $proposal = Proposal::query()
            ->where('public_token', $token)
            ->with(['template', 'settings', 'sections', 'pricingItems', 'timelines', 'teamMembers'])
            ->firstOrFail();

        if ($proposal->status === 'sent') {
            $proposal->update(['status' => 'viewed']);
        }

        return view('admin.proposals.share', [
            'proposal' => $proposal,
        ]);
    }

    public function improve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mode' => ['nullable', Rule::in(['generate', 'improve'])],
            'type' => ['nullable', 'string', 'max:80'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:5000'],
            'proposal_title' => ['nullable', 'string', 'max:255'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json([
            'content' => $this->composeAssistedCopy($validated),
        ]);
    }

    private function builderView(?Proposal $builderProposal = null): View
    {
        $templates = ProposalTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $proposals = Proposal::query()
            ->with('template')
            ->latest()
            ->limit(12)
            ->get();

        $statusCounts = Proposal::query()
            ->select('status')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($count): int => (int) $count)
            ->all();

        $totalValue = (float) Proposal::query()->sum('grand_total');

        return view('admin.proposals.index', [
            'templates' => $templates,
            'proposals' => $proposals,
            'builderProposal' => $builderProposal,
            'builderState' => $this->builderState($builderProposal),
            'statuses' => self::STATUSES,
            'statusCounts' => $statusCounts,
            'totalValue' => $totalValue,
        ]);
    }

    private function validateProposalRequest(Request $request): array
    {
        return $request->validate([
            'proposal_template_id' => ['required', 'integer', 'exists:proposal_templates,id'],
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'title' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_slogan' => ['nullable', 'string', 'max:255'],
            'proposal_date' => ['nullable', 'date'],
            'reference_number' => ['nullable', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:80'],
            'website' => ['nullable', 'string', 'max:255'],
            'business_address' => ['nullable', 'string', 'max:1000'],
            'currency' => ['nullable', 'string', 'max:10'],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_family' => ['required', 'string', 'max:120'],
            'header_style' => ['required', 'string', 'max:120'],
            'footer_style' => ['required', 'string', 'max:120'],
            'watermark' => ['nullable', 'string', 'max:120'],
            'page_numbering' => ['nullable', 'boolean'],
            'sections_payload' => ['required', 'json'],
            'pricing_payload' => ['nullable', 'json'],
            'timeline_payload' => ['nullable', 'json'],
            'team_payload' => ['nullable', 'json'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'cover_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:8192'],
            'background_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:8192'],
        ]);
    }

    private function proposalAttributes(
        array $validated,
        ProposalTemplate $template,
        array $totals,
        ?Proposal $proposal = null
    ): array {
        return [
            'user_id' => auth()->id(),
            'proposal_template_id' => $template->id,
            'proposal_number' => $proposal?->proposal_number ?? $this->generateProposalNumber(),
            'public_token' => $proposal?->public_token ?? Str::random(48),
            'status' => $validated['status'] ?? 'draft',
            'theme_key' => $template->theme_key,
            'title' => $validated['title'],
            'client_name' => $validated['client_name'] ?? null,
            'client_company' => $validated['client_company'] ?? null,
            'prepared_by' => $validated['prepared_by'] ?? null,
            'company_name' => $validated['company_name'],
            'company_slogan' => $validated['company_slogan'] ?? null,
            'proposal_date' => $validated['proposal_date'] ?? now()->toDateString(),
            'reference_number' => filled($validated['reference_number'] ?? null)
                ? $validated['reference_number']
                : ($proposal?->reference_number ?? $this->generateReferenceNumber()),
            'contact_email' => $validated['contact_email'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'website' => $validated['website'] ?? null,
            'business_address' => $validated['business_address'] ?? null,
            'currency' => $validated['currency'] ?? 'USD',
            'subtotal' => $totals['subtotal'],
            'discount_total' => $totals['discount_total'],
            'tax_total' => $totals['tax_total'],
            'grand_total' => $totals['grand_total'],
            'metadata' => [
                'source' => 'proposal_generator',
                'template_slug' => $template->slug,
                'share_enabled' => true,
            ],
        ];
    }

    private function syncProposalChildren(Proposal $proposal, Request $request, array $pricingItems): void
    {
        $proposal->settings()->updateOrCreate([], [
            'primary_color' => $request->input('primary_color'),
            'secondary_color' => $request->input('secondary_color'),
            'accent_color' => $request->input('accent_color'),
            'font_family' => $request->input('font_family'),
            'header_style' => $request->input('header_style'),
            'footer_style' => $request->input('footer_style'),
            'page_numbering' => (bool) $request->boolean('page_numbering'),
            'watermark' => $request->input('watermark'),
            'options' => [
                'desktop_preview' => true,
                'print_ready' => true,
            ],
        ]);

        $proposal->sections()->delete();
        foreach ($this->normalizeSections($this->decodePayload($request->input('sections_payload'), config('proposals.sections', []))) as $section) {
            $proposal->sections()->create($section);
        }

        $proposal->pricingItems()->delete();
        foreach ($pricingItems as $item) {
            $proposal->pricingItems()->create($item);
        }

        $proposal->timelines()->delete();
        foreach ($this->normalizeTimelineItems($this->decodePayload($request->input('timeline_payload'), config('proposals.timeline', []))) as $item) {
            $proposal->timelines()->create($item);
        }

        $proposal->teamMembers()->delete();
        foreach ($this->normalizeTeamMembers($this->decodePayload($request->input('team_payload'), config('proposals.team', []))) as $member) {
            $proposal->teamMembers()->create($member);
        }
    }

    private function storeUploadedAsset(Proposal $proposal, Request $request, string $field, string $type, string $column): void
    {
        if (! $request->hasFile($field)) {
            return;
        }

        $file = $request->file($field);
        $path = $file->store("proposals/{$proposal->id}", 'public');

        $proposal->update([$column => $path]);
        $proposal->assets()->create([
            'asset_type' => $type,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    private function normalizeSections(array $sections): array
    {
        $normalized = collect($sections)
            ->map(function (array $section, int $index): array {
                return [
                    'type' => Str::limit((string) ($section['type'] ?? 'custom'), 80, ''),
                    'title' => Str::limit(trim((string) ($section['title'] ?? 'Proposal Section')), 255, ''),
                    'eyebrow' => Str::limit(trim((string) ($section['eyebrow'] ?? '')), 120, ''),
                    'body' => trim((string) ($section['body'] ?? '')),
                    'payload' => is_array($section['payload'] ?? null) ? $section['payload'] : [],
                    'layout_style' => Str::limit((string) ($section['layout_style'] ?? 'editorial'), 80, ''),
                    'is_visible' => (bool) ($section['is_visible'] ?? true),
                    'sort_order' => $index,
                ];
            })
            ->filter(fn (array $section): bool => $section['title'] !== '')
            ->values()
            ->all();

        return $normalized !== [] ? $normalized : $this->normalizeSections(config('proposals.sections', []));
    }

    private function normalizePricingItems(array $items): array
    {
        $subtotal = 0.0;
        $discountTotal = 0.0;
        $taxTotal = 0.0;

        $pricingItems = collect($items)
            ->map(function (array $item, int $index) use (&$subtotal, &$discountTotal, &$taxTotal): array {
                $quantity = max(0.0, (float) ($item['quantity'] ?? 1));
                $unitPrice = max(0.0, (float) ($item['unit_price'] ?? 0));
                $base = round($quantity * $unitPrice, 2);
                $discount = min(max(0.0, (float) ($item['discount'] ?? 0)), $base);
                $taxRate = max(0.0, (float) ($item['tax_rate'] ?? 0));
                $tax = round(($base - $discount) * ($taxRate / 100), 2);
                $lineTotal = round($base - $discount + $tax, 2);

                $subtotal += $base;
                $discountTotal += $discount;
                $taxTotal += $tax;

                return [
                    'package' => Str::limit((string) ($item['package'] ?? 'Custom'), 40, ''),
                    'service_name' => Str::limit(trim((string) ($item['service_name'] ?? 'Proposal service')), 255, ''),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax_rate' => $taxRate,
                    'line_total' => $lineTotal,
                    'sort_order' => $index,
                ];
            })
            ->filter(fn (array $item): bool => $item['service_name'] !== '' && $item['line_total'] >= 0)
            ->values()
            ->all();

        if ($pricingItems === []) {
            return $this->normalizePricingItems(config('proposals.pricing_items', []));
        }

        return [
            $pricingItems,
            [
                'subtotal' => round($subtotal, 2),
                'discount_total' => round($discountTotal, 2),
                'tax_total' => round($taxTotal, 2),
                'grand_total' => round($subtotal - $discountTotal + $taxTotal, 2),
            ],
        ];
    }

    private function normalizeTimelineItems(array $items): array
    {
        return collect($items)
            ->map(fn (array $item, int $index): array => [
                'phase_title' => Str::limit(trim((string) ($item['phase_title'] ?? 'Project Phase')), 255, ''),
                'description' => trim((string) ($item['description'] ?? '')),
                'start_date' => filled($item['start_date'] ?? null) ? $item['start_date'] : null,
                'end_date' => filled($item['end_date'] ?? null) ? $item['end_date'] : null,
                'duration' => Str::limit(trim((string) ($item['duration'] ?? '')), 120, ''),
                'deliverables' => trim((string) ($item['deliverables'] ?? '')),
                'status' => Str::limit(trim((string) ($item['status'] ?? 'Planned')), 40, ''),
                'sort_order' => $index,
            ])
            ->filter(fn (array $item): bool => $item['phase_title'] !== '')
            ->values()
            ->all();
    }

    private function normalizeTeamMembers(array $items): array
    {
        return collect($items)
            ->map(fn (array $item, int $index): array => [
                'name' => Str::limit(trim((string) ($item['name'] ?? 'Team Member')), 255, ''),
                'role' => Str::limit(trim((string) ($item['role'] ?? 'Project Team')), 255, ''),
                'bio' => trim((string) ($item['bio'] ?? '')),
                'profile_image_path' => trim((string) ($item['profile_image_path'] ?? '')),
                'email' => Str::limit(trim((string) ($item['email'] ?? '')), 255, ''),
                'social_link' => Str::limit(trim((string) ($item['social_link'] ?? '')), 255, ''),
                'sort_order' => $index,
            ])
            ->filter(fn (array $item): bool => $item['name'] !== '')
            ->values()
            ->all();
    }

    private function decodePayload(?string $payload, array $fallback = []): array
    {
        if (! filled($payload)) {
            return $fallback;
        }

        $decoded = json_decode((string) $payload, true);

        return is_array($decoded) ? $decoded : $fallback;
    }

    private function builderState(?Proposal $proposal): array
    {
        $defaultTemplate = ProposalTemplate::query()->where('is_active', true)->orderBy('sort_order')->first();
        $palette = $defaultTemplate?->palette ?? [];
        $settings = $defaultTemplate?->settings ?? [];

        if (! $proposal) {
            return [
                'proposal' => [
                    'proposal_template_id' => $defaultTemplate?->id,
                    'status' => 'draft',
                    'title' => 'Business Enterprise Proposal',
                    'client_name' => '',
                    'client_company' => '',
                    'prepared_by' => config('luxury-quotes.brand.studio_name', 'Turance Technologies'),
                    'company_name' => config('luxury-quotes.brand.studio_name', 'Turance Technologies'),
                    'company_slogan' => config('luxury-quotes.brand.tagline', 'Excellence Delivered'),
                    'proposal_date' => now()->toDateString(),
                    'reference_number' => $this->generateReferenceNumber(),
                    'contact_email' => config('luxury-quotes.brand.contact_email'),
                    'phone_number' => config('luxury-quotes.brand.contact_phone'),
                    'website' => config('luxury-quotes.brand.website'),
                    'business_address' => '',
                    'currency' => 'USD',
                ],
                'settings' => [
                    'primary_color' => $palette['primary'] ?? '#111111',
                    'secondary_color' => $palette['secondary'] ?? '#f3f4f0',
                    'accent_color' => $palette['accent'] ?? '#e8b51f',
                    'font_family' => $settings['font_family'] ?? 'Aptos',
                    'header_style' => $settings['header_style'] ?? 'Editorial split',
                    'footer_style' => $settings['footer_style'] ?? 'Gold folio',
                    'page_numbering' => true,
                    'watermark' => '',
                ],
                'sections' => $this->normalizeSections(config('proposals.sections', [])),
                'pricing' => $this->normalizePricingItems(config('proposals.pricing_items', []))[0],
                'timeline' => $this->normalizeTimelineItems(config('proposals.timeline', [])),
                'team' => $this->normalizeTeamMembers(config('proposals.team', [])),
                'asset_urls' => [],
            ];
        }

        return [
            'proposal' => [
                'proposal_template_id' => $proposal->proposal_template_id,
                'status' => $proposal->status,
                'title' => $proposal->title,
                'client_name' => $proposal->client_name,
                'client_company' => $proposal->client_company,
                'prepared_by' => $proposal->prepared_by,
                'company_name' => $proposal->company_name,
                'company_slogan' => $proposal->company_slogan,
                'proposal_date' => optional($proposal->proposal_date)->toDateString(),
                'reference_number' => $proposal->reference_number,
                'contact_email' => $proposal->contact_email,
                'phone_number' => $proposal->phone_number,
                'website' => $proposal->website,
                'business_address' => $proposal->business_address,
                'currency' => $proposal->currency,
            ],
            'settings' => [
                'primary_color' => $proposal->settings?->primary_color ?? '#111111',
                'secondary_color' => $proposal->settings?->secondary_color ?? '#f3f4f0',
                'accent_color' => $proposal->settings?->accent_color ?? '#e8b51f',
                'font_family' => $proposal->settings?->font_family ?? 'Aptos',
                'header_style' => $proposal->settings?->header_style ?? 'Editorial split',
                'footer_style' => $proposal->settings?->footer_style ?? 'Gold folio',
                'page_numbering' => (bool) ($proposal->settings?->page_numbering ?? true),
                'watermark' => $proposal->settings?->watermark ?? '',
            ],
            'sections' => $proposal->sections->map(fn ($section): array => [
                'type' => $section->type,
                'title' => $section->title,
                'eyebrow' => $section->eyebrow,
                'body' => $section->body,
                'payload' => $section->payload ?? [],
                'layout_style' => $section->layout_style,
                'is_visible' => (bool) $section->is_visible,
                'sort_order' => $section->sort_order,
            ])->values()->all(),
            'pricing' => $proposal->pricingItems->map(fn ($item): array => [
                'package' => $item->package,
                'service_name' => $item->service_name,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'discount' => (float) $item->discount,
                'tax_rate' => (float) $item->tax_rate,
                'line_total' => (float) $item->line_total,
            ])->values()->all(),
            'timeline' => $proposal->timelines->map(fn ($item): array => [
                'phase_title' => $item->phase_title,
                'description' => $item->description,
                'start_date' => optional($item->start_date)->toDateString(),
                'end_date' => optional($item->end_date)->toDateString(),
                'duration' => $item->duration,
                'deliverables' => $item->deliverables,
                'status' => $item->status,
            ])->values()->all(),
            'team' => $proposal->teamMembers->map(fn ($member): array => [
                'name' => $member->name,
                'role' => $member->role,
                'bio' => $member->bio,
                'profile_image_path' => $member->profile_image_path,
                'email' => $member->email,
                'social_link' => $member->social_link,
            ])->values()->all(),
            'asset_urls' => [
                'logo' => $proposal->logo_path ? Storage::disk('public')->url($proposal->logo_path) : null,
                'cover_image' => $proposal->cover_image_path ? Storage::disk('public')->url($proposal->cover_image_path) : null,
                'background_image' => $proposal->background_image_path ? Storage::disk('public')->url($proposal->background_image_path) : null,
            ],
        ];
    }

    private function loadProposalDocument(Proposal $proposal): Proposal
    {
        return $proposal->load(['template', 'settings', 'sections', 'pricingItems', 'timelines', 'teamMembers', 'exports']);
    }

    private function syncDefaultTemplates(): void
    {
        if (! Schema::hasTable('proposal_templates')) {
            return;
        }

        foreach (config('proposals.templates', []) as $slug => $template) {
            ProposalTemplate::query()->updateOrCreate([
                'slug' => $slug,
            ], [
                'name' => $template['name'],
                'category' => $template['category'] ?? null,
                'theme_key' => $template['theme_key'] ?? 'gold',
                'description' => $template['description'] ?? null,
                'palette' => $template['palette'] ?? [],
                'settings' => $template['settings'] ?? [],
                'preview' => $template['preview'] ?? [],
                'is_active' => true,
                'sort_order' => $template['sort_order'] ?? 0,
            ]);
        }
    }

    private function generateProposalNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = Proposal::withTrashed()
            ->whereDate('created_at', today())
            ->count() + 1;

        do {
            $number = sprintf('TT-PROP-%s-%03d', $date, $sequence);
            $sequence++;
        } while (Proposal::withTrashed()->where('proposal_number', $number)->exists());

        return $number;
    }

    private function generateReferenceNumber(): string
    {
        return 'PROP-'.Carbon::now()->format('ymd').'-'.Str::upper(Str::random(4));
    }

    private function composeAssistedCopy(array $context): string
    {
        $mode = $context['mode'] ?? 'improve';
        $type = $context['type'] ?? 'executive_summary';
        $title = $context['title'] ?? Str::headline($type);
        $existing = trim((string) ($context['body'] ?? ''));
        $company = filled($context['company_name'] ?? null) ? $context['company_name'] : 'our team';
        $client = filled($context['client_company'] ?? null) ? $context['client_company'] : 'your organization';
        $proposalTitle = filled($context['proposal_title'] ?? null) ? $context['proposal_title'] : 'this engagement';

        $base = match ($type) {
            'executive_summary' => "{$company} proposes a focused engagement for {$client} that clarifies the opportunity, defines a practical path forward, and delivers {$proposalTitle} with strong commercial discipline. The work is structured to reduce ambiguity, improve stakeholder confidence, and create a polished outcome that can be reviewed, approved, and implemented without unnecessary friction.",
            'about_company' => "{$company} combines strategy, design judgment, and disciplined execution to help clients turn business goals into clear, credible experiences. Our approach is collaborative, detail-oriented, and built around outcomes that matter to executives, teams, and end customers.",
            'problem_statement' => "{$client} needs a solution that communicates value more clearly, removes operational uncertainty, and gives decision-makers a stronger basis for action. The current opportunity calls for a premium, structured response that protects quality while keeping the project moving.",
            'proposed_solution' => "We recommend a phased solution that begins with alignment, moves into structured execution, and finishes with review, refinement, and handoff. This creates a clear project rhythm, gives stakeholders visible checkpoints, and ensures the final output is polished enough for client-facing use.",
            'scope_of_work' => "The scope includes discovery, requirements alignment, content and asset coordination, design direction, implementation support, quality review, and final handoff. Each workstream is organized to keep responsibilities clear and make progress measurable throughout the engagement.",
            'services_offered' => "{$company} will provide the services required to move {$proposalTitle} from planning into delivery, including strategic direction, creative development, implementation support, documentation, stakeholder review, and post-delivery refinement where required.",
            'timeline' => "The project timeline is arranged into clear phases with defined outputs, review points, and delivery responsibilities. This structure helps {$client} understand what happens next, when decisions are needed, and how each milestone supports the final result.",
            'pricing' => "The investment below reflects the planned scope, level of expertise, delivery support, and quality control required to complete the engagement professionally. Each item is organized so {$client} can review the commercial structure with clarity.",
            'terms' => "Work begins after written acceptance and receipt of the agreed initial payment. Timelines depend on timely approvals, access to required materials, and coordinated stakeholder feedback. Any material scope changes will be reviewed and priced before implementation.",
            'closing' => "{$company} is ready to move forward with a clear plan, a professional delivery rhythm, and a shared commitment to a strong final outcome. We appreciate the opportunity to support {$client} and look forward to beginning the next phase.",
            default => "{$title} has been prepared to give {$client} a clear, professional view of the proposed engagement. This section explains the recommended direction, the value it creates, and the practical steps needed to move forward with confidence.",
        };

        if ($mode === 'generate' || $existing === '') {
            return $base;
        }

        return $existing.' '.$base.' The revised wording should be used as a polished client-facing section, with enough detail to support decision-making while keeping the proposal direct and easy to approve.';
    }
}
