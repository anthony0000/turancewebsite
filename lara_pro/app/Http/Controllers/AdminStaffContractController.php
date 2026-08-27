<?php

namespace App\Http\Controllers;

use App\Models\LuxuryQuote;
use App\Models\Project;
use App\Models\StaffContract;
use App\Support\DocumentTypography;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminStaffContractController extends Controller
{
    private const SIGNED_DOCUMENT_DIRECTORY = 'staff-contracts/signed-documents';

    private const STATUSES = [
        'draft',
        'pending_signature',
        'signed',
        'active',
        'completed',
        'terminated',
    ];

    public function index(): View
    {
        $contracts = StaffContract::query()
            ->with(['project', 'invoice'])
            ->latest('updated_at')
            ->get();

        $statusBreakdown = collect(self::STATUSES)
            ->map(fn (string $status) => [
                'label' => Str::headline($status),
                'count' => $contracts->where('status', $status)->count(),
            ])
            ->filter(fn (array $status) => $status['count'] > 0)
            ->values();

        $activityStart = now()->startOfMonth()->subMonths(5);
        $monthlyActivity = collect(range(0, 5))
            ->map(function (int $offset) use ($activityStart, $contracts): array {
                $month = $activityStart->copy()->addMonths($offset);

                return [
                    'label' => $month->format('M'),
                    'period' => $month->format('F Y'),
                    'count' => $contracts
                        ->filter(fn (StaffContract $contract): bool => $contract->created_at?->isSameMonth($month) ?? false)
                        ->count(),
                ];
            });

        $maxMonthlyActivity = max(1, (int) $monthlyActivity->max('count'));
        $monthlyActivity = $monthlyActivity->map(fn (array $month): array => $month + [
            'height' => round(($month['count'] / $maxMonthlyActivity) * 100, 1),
        ]);

        $currencyBreakdown = $contracts
            ->groupBy(function (StaffContract $contract): string {
                $currency = strtoupper(trim((string) $contract->currency));

                return $currency !== '' ? $currency : 'N/A';
            })
            ->map(function ($currencyContracts, string $currency): array {
                return [
                    'currency' => $currency,
                    'count' => $currencyContracts->count(),
                    'value' => (float) $currencyContracts->sum(fn (StaffContract $contract): float => (float) $contract->agreed_fee),
                ];
            })
            ->sortByDesc('value')
            ->values();

        $maxCurrencyValue = max(1, (float) $currencyBreakdown->max('value'));
        $currencyBreakdown = $currencyBreakdown->map(fn (array $currency): array => $currency + [
            'width' => round(($currency['value'] / $maxCurrencyValue) * 100, 1),
        ]);

        return view('admin.staff-contracts.index', [
            'contracts' => $contracts,
            'statusBreakdown' => $statusBreakdown,
            'monthlyActivity' => $monthlyActivity,
            'currencyBreakdown' => $currencyBreakdown,
            'maxCurrencyValue' => $maxCurrencyValue,
            'invoiceCount' => LuxuryQuote::query()->count(),
            'activeCount' => $contracts->whereIn('status', ['pending_signature', 'signed', 'active'])->count(),
            'signedCount' => $contracts->where('status', 'signed')->count(),
            'totalValue' => (float) $contracts->sum(fn (StaffContract $contract): float => (float) $contract->agreed_fee),
        ]);
    }

    public function create(): View
    {
        return view('admin.staff-contracts.create', [
            'invoices' => LuxuryQuote::query()->latest('created_at')->get(),
            'contract' => null,
            'statuses' => self::STATUSES,
            'brand' => config('luxury-quotes.brand', []),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateContractRequest($request);

        $contract = DB::transaction(function () use ($validated): StaffContract {
            $invoice = LuxuryQuote::query()->findOrFail($validated['luxury_quote_id']);
            $project = $this->projectForInvoice($invoice);

            return StaffContract::query()->create($this->contractAttributes(
                $validated,
                $project,
                $invoice,
                $this->generateContractNumber(),
            ));
        });

        if ($request->hasFile('signed_document')) {
            $this->replaceSignedDocument($contract, $request->file('signed_document'));
        }

        return redirect()
            ->route('admin.staff-contracts.show', $contract)
            ->with('status', "Staff contract {$contract->contract_number} created.");
    }

    public function show(StaffContract $staffContract): View
    {
        $contract = $staffContract->load(['project', 'invoice']);

        return view('admin.staff-contracts.show', [
            'contract' => $contract,
            'brand' => config('luxury-quotes.brand', []),
        ]);
    }

    public function edit(StaffContract $staffContract): View|RedirectResponse
    {
        if ($staffContract->hasSignedDocument()) {
            return redirect()
                ->route('admin.staff-contracts.show', $staffContract)
                ->with('error', 'This contract is locked because its signed document has already been uploaded.');
        }

        return view('admin.staff-contracts.create', [
            'invoices' => LuxuryQuote::query()->latest('created_at')->get(),
            'contract' => $staffContract->load(['project', 'invoice']),
            'statuses' => self::STATUSES,
            'brand' => config('luxury-quotes.brand', []),
        ]);
    }

    public function update(Request $request, StaffContract $staffContract): RedirectResponse
    {
        if ($staffContract->hasSignedDocument()) {
            return redirect()
                ->route('admin.staff-contracts.show', $staffContract)
                ->with('error', 'This contract is locked because its signed document has already been uploaded.');
        }

        $validated = $this->validateContractRequest($request);
        $invoice = LuxuryQuote::query()->findOrFail($validated['luxury_quote_id']);

        $staffContract->update($this->contractAttributes(
            $validated,
            $this->projectForInvoice($invoice),
            $invoice,
            $staffContract->contract_number,
            $staffContract,
        ));

        if ($request->hasFile('signed_document')) {
            $this->replaceSignedDocument($staffContract, $request->file('signed_document'));
        }

        return redirect()
            ->route('admin.staff-contracts.show', $staffContract)
            ->with('status', "Staff contract {$staffContract->contract_number} updated.");
    }

    public function downloadPdf(StaffContract $staffContract): Response
    {
        $staffContract->load(['project', 'invoice']);

        $pdf = Pdf::loadView('admin.staff-contracts.pdf', [
            'contract' => $staffContract,
            'brand' => config('luxury-quotes.brand', []),
        ])->setPaper('a4');
        DocumentTypography::registerDompdfFonts($pdf->getDomPDF());

        return $pdf->download(Str::slug($staffContract->contract_number.' '.$staffContract->staff_name).'.pdf');
    }

    public function downloadSignedDocument(StaffContract $staffContract): BinaryFileResponse|Response
    {
        return $this->signedDocumentResponse($staffContract, false);
    }

    public function previewSignedDocument(StaffContract $staffContract): BinaryFileResponse|Response
    {
        return $this->signedDocumentResponse($staffContract, true);
    }

    private function signedDocumentResponse(StaffContract $contract, bool $inline): BinaryFileResponse|Response
    {
        abort_unless($contract->hasSignedDocument(), 404);

        $filename = $contract->signed_document_original_name
            ?: Str::slug($contract->contract_number).'.document';
        $headers = [
            'Content-Type' => $contract->signed_document_mime ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ];
        $content = $contract->signedDocumentContent()->first(['contents']);

        if ($content !== null) {
            $headers['Content-Disposition'] = ($inline ? 'inline' : 'attachment').'; filename="'.addslashes($filename).'"';

            if ($contract->signed_document_size !== null) {
                $headers['Content-Length'] = (string) $contract->signed_document_size;
            }

            return response($content->contents, 200, $headers);
        }

        $absolutePath = $this->signedDocumentAbsolutePath($contract);

        if ($inline) {
            $headers['Content-Disposition'] = 'inline; filename="'.addslashes($filename).'"';

            return response()->file($absolutePath, $headers);
        }

        return response()->download($absolutePath, $filename, $headers);
    }

    private function signedDocumentAbsolutePath(StaffContract $contract): string
    {
        abort_unless($contract->hasSignedDocument(), 404);

        $relativePath = ltrim((string) $contract->signed_document_path, '/\\\\');
        $disk = Storage::disk('local');

        if ($disk->exists($relativePath)) {
            return $disk->path($relativePath);
        }

        // Keep documents uploaded before the private local disk was enabled
        // available after deployment if they remain under storage/app.
        foreach ([storage_path('app'), storage_path('app/private'), storage_path('app/public')] as $root) {
            $legacyPath = $root.DIRECTORY_SEPARATOR.$relativePath;

            if (is_file($legacyPath)) {
                return $legacyPath;
            }
        }

        abort(404);
    }

    private function validateContractRequest(Request $request): array
    {
        return $request->validate([
            'luxury_quote_id' => ['required', 'integer', 'exists:luxury_quotes,id'],
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
            'staff_name' => ['required', 'string', 'max:255'],
            'staff_email' => ['nullable', 'email', 'max:255'],
            'staff_phone' => ['nullable', 'string', 'max:80'],
            'staff_role' => ['required', 'string', 'max:255'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'currency' => ['required', 'string', 'size:3'],
            'agreed_fee' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'payment_terms' => ['required', 'string', 'min:10', 'max:5000'],
            'scope_of_work' => ['required', 'string', 'min:10', 'max:15000'],
            'terms' => ['required', 'string', 'min:20', 'max:20000'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_signatory_name' => ['nullable', 'string', 'max:255'],
            'company_signatory_title' => ['nullable', 'string', 'max:255'],
            'company_signed_date' => ['nullable', 'date'],
            'staff_signatory_name' => ['nullable', 'string', 'max:255'],
            'staff_signed_date' => ['nullable', 'date'],
            'signed_document' => ['nullable', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function replaceSignedDocument(StaffContract $contract, UploadedFile $file): void
    {
        $filename = $this->signedDocumentFilename($contract, $file);
        $path = self::SIGNED_DOCUMENT_DIRECTORY.'/'.$filename;
        $contents = $file->getContent();
        $oldPath = $contract->signed_document_path;

        DB::transaction(function () use ($contract, $path, $filename, $file, $contents): void {
            $contract->forceFill([
                'signed_document_path' => $path,
                'signed_document_original_name' => $filename,
                'signed_document_mime' => $file->getMimeType() ?: $file->getClientMimeType(),
                'signed_document_size' => $file->getSize() ?: null,
            ])->save();

            $contract->signedDocumentContent()->updateOrCreate([], [
                'contents' => $contents,
            ]);
        });

        if (filled($oldPath) && $oldPath !== $path) {
            Storage::disk('local')->delete($oldPath);
        }
    }

    private function signedDocumentFilename(StaffContract $contract, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'document');
        $extension = preg_replace('/[^a-z0-9]+/i', '', $extension) ?: 'document';

        return Str::slug($contract->contract_number.' '.$contract->staff_name.' signed').'.'.$extension;
    }

    private function projectForInvoice(LuxuryQuote $invoice): Project
    {
        $projectName = trim($invoice->project_title);
        $clientCompany = $this->nullableTrim($invoice->company_name);

        return Project::query()->firstOrCreate(
            [
                'name' => $projectName,
                'client_company' => $clientCompany,
            ],
            [
                'project_number' => $this->generateProjectNumber(),
                'client_name' => $this->nullableTrim($invoice->recipient_name),
                'status' => 'active',
                'description' => $this->nullableTrim($invoice->executive_summary),
            ],
        );
    }

    private function contractAttributes(
        array $validated,
        Project $project,
        LuxuryQuote $invoice,
        string $contractNumber,
        ?StaffContract $existing = null,
    ): array {
        return [
            'project_id' => $project->id,
            'luxury_quote_id' => $invoice->id,
            'contract_number' => $contractNumber,
            'status' => $validated['status'],
            'staff_name' => trim($validated['staff_name']),
            'staff_email' => $this->optionalContractString($validated, 'staff_email', $existing?->staff_email),
            'staff_phone' => $this->optionalContractString($validated, 'staff_phone', $existing?->staff_phone),
            'staff_role' => trim($validated['staff_role']),
            'starts_on' => $this->optionalContractDate($validated, 'starts_on', $existing?->starts_on),
            'ends_on' => $this->optionalContractDate($validated, 'ends_on', $existing?->ends_on),
            'currency' => strtoupper(trim($validated['currency'])),
            'agreed_fee' => round((float) $validated['agreed_fee'], 2),
            'payment_terms' => trim($validated['payment_terms']),
            'scope_of_work' => $this->sanitizeRichText($validated['scope_of_work']),
            'terms' => $this->sanitizeRichText($validated['terms']),
            'company_name' => $this->nullableTrim($validated['company_name'] ?? null)
                ?: ($existing?->company_name ?: config('luxury-quotes.brand.studio_name', 'Turance Technologies')),
            'company_signatory_name' => $this->optionalContractString($validated, 'company_signatory_name', $existing?->company_signatory_name),
            'company_signatory_title' => $this->optionalContractString($validated, 'company_signatory_title', $existing?->company_signatory_title),
            'company_signed_date' => $this->optionalContractDate($validated, 'company_signed_date', $existing?->company_signed_date),
            'staff_signatory_name' => $this->optionalContractString($validated, 'staff_signatory_name', $existing?->staff_signatory_name),
            'staff_signed_date' => $this->optionalContractDate($validated, 'staff_signed_date', $existing?->staff_signed_date),
            'notes' => $this->optionalContractString($validated, 'notes', $existing?->notes),
        ];
    }

    private function generateProjectNumber(): string
    {
        do {
            $number = 'TT-PRJ-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (Project::query()->where('project_number', $number)->exists());

        return $number;
    }

    private function generateContractNumber(): string
    {
        do {
            $number = 'TT-STAFF-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (StaffContract::query()->where('contract_number', $number)->exists());

        return $number;
    }

    private function nullableTrim(?string $value): ?string
    {
        return filled($value) ? trim($value) : null;
    }

    private function optionalContractString(array $validated, string $key, ?string $existing): ?string
    {
        return array_key_exists($key, $validated)
            ? $this->nullableTrim($validated[$key] ?? null)
            : $existing;
    }

    private function optionalContractDate(array $validated, string $key, mixed $existing): mixed
    {
        return array_key_exists($key, $validated)
            ? $this->nullableDate($validated[$key] ?? null)
            : $existing;
    }

    private function nullableDate(?string $value): ?Carbon
    {
        return filled($value) ? Carbon::parse($value) : null;
    }

    private function sanitizeRichText(?string $value): string
    {
        $html = trim((string) $value);

        if ($html === '') {
            return '';
        }

        $html = preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', '', $html) ?? '';
        $html = strip_tags($html, '<p><div><br><ul><ol><li><strong><b><em><i><u>');
        $html = preg_replace('/<([a-z][a-z0-9]*)\b[^>]*>/i', '<$1>', $html) ?? $html;

        return trim(str_ireplace(
            ['<b>', '</b>', '<i>', '</i>', '<br/>', '<br />'],
            ['<strong>', '</strong>', '<em>', '</em>', '<br>', '<br>'],
            $html
        ));
    }
}
