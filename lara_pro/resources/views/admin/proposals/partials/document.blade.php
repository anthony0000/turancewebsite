@php
    use App\Support\DocumentBranding;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $settings = $proposal->settings;
    $isPdfExport = (bool) ($isPdfExport ?? false);
    $assetMode = $assetMode ?? (($isPdfExport || request()->routeIs('admin.proposals.pdf')) ? 'local' : 'url');

    $fileUri = function (string $path): string {
        $path = str_replace('\\', '/', $path);
        $segments = array_map('rawurlencode', explode('/', $path));

        if (isset($segments[0]) && preg_match('/^[A-Za-z]%3A$/', $segments[0]) === 1) {
            $segments[0] = str_replace('%3A', ':', $segments[0]);
        }

        return 'file:///'.implode('/', $segments);
    };

    $assetSrc = function (?string $path) use ($assetMode, $fileUri): ?string {
        if (! filled($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        $localPath = storage_path('app/public/'.$path);

        if ($assetMode === 'file-uri' && file_exists($localPath)) {
            return $fileUri($localPath);
        }

        if ($assetMode === 'local' && file_exists($localPath)) {
            return $localPath;
        }

        return Storage::disk('public')->url($path);
    };

    $sectionSource = $proposal->sections->isNotEmpty()
        ? $proposal->sections
        : collect(config('proposals.sections', []))->map(fn ($section, $index) => (object) array_merge($section, [
            'payload' => [],
            'layout_style' => 'editorial',
            'is_visible' => true,
            'sort_order' => $index,
        ]));

    $hasSectionContent = function ($section) use ($proposal): bool {
        if (! (bool) ($section->is_visible ?? true)) {
            return false;
        }

        $type = (string) ($section->type ?? 'custom');
        $payload = $section->payload ?? [];
        $hasText = filled($section->title ?? null) || filled($section->eyebrow ?? null) || filled($section->body ?? null);
        $hasPayload = is_array($payload) && collect($payload)->filter(fn ($value) => filled($value))->isNotEmpty();

        return match ($type) {
            'cover', 'table_of_contents' => true,
            'pricing' => $hasText || $proposal->pricingItems->isNotEmpty(),
            'timeline', 'work_process' => $hasText || $proposal->timelines->isNotEmpty(),
            'team' => $hasText || $proposal->teamMembers->isNotEmpty(),
            default => $hasText || $hasPayload,
        };
    };

    $visibleSections = collect($sectionSource)->filter($hasSectionContent)->values();

    $splitText = function (?string $text, int $limit = 1150): array {
        $text = trim((string) preg_replace('/\s+/', ' ', (string) $text));

        if ($text === '') {
            return [''];
        }

        $chunks = [];
        $current = '';

        foreach (preg_split('/\s+/', $text) as $word) {
            $candidate = trim($current.' '.$word);

            if ($current !== '' && strlen($candidate) > $limit) {
                $chunks[] = $current;
                $current = $word;

                continue;
            }

            $current = $candidate;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks !== [] ? $chunks : [''];
    };

    $documentPages = collect();

    foreach ($visibleSections as $sectionIndex => $section) {
        $sectionType = (string) ($section->type ?? 'custom');

        if ($sectionType === 'cover') {
            $documentPages->push([
                'kind' => 'cover',
                'section' => $section,
                'section_index' => $sectionIndex,
                'section_type' => $sectionType,
                'continuation' => false,
            ]);

            continue;
        }

        if ($sectionType === 'pricing') {
            $pricingChunks = $proposal->pricingItems->values()->chunk(7)->values();
            $pricingChunks = $pricingChunks->isNotEmpty() ? $pricingChunks : collect([collect()]);
            $lastPricingChunk = max(0, $pricingChunks->count() - 1);

            foreach ($pricingChunks as $chunkIndex => $items) {
                $documentPages->push([
                    'kind' => 'pricing',
                    'section' => $section,
                    'section_index' => $sectionIndex,
                    'section_type' => $sectionType,
                    'pricing_items' => $items,
                    'show_intro' => $chunkIndex === 0,
                    'show_totals' => $chunkIndex === $lastPricingChunk,
                    'continuation' => $chunkIndex > 0,
                ]);
            }

            continue;
        }

        if (in_array($sectionType, ['timeline', 'work_process'], true)) {
            $timelineChunks = $proposal->timelines->values()->chunk(4)->values();
            $timelineChunks = $timelineChunks->isNotEmpty() ? $timelineChunks : collect([collect()]);

            foreach ($timelineChunks as $chunkIndex => $items) {
                $documentPages->push([
                    'kind' => 'timeline',
                    'section' => $section,
                    'section_index' => $sectionIndex,
                    'section_type' => $sectionType,
                    'timeline_items' => $items,
                    'timeline_offset' => $chunkIndex * 4,
                    'show_intro' => $chunkIndex === 0,
                    'continuation' => $chunkIndex > 0,
                ]);
            }

            continue;
        }

        if ($sectionType === 'team') {
            $teamChunks = $proposal->teamMembers->values()->chunk(3)->values();
            $teamChunks = $teamChunks->isNotEmpty() ? $teamChunks : collect([collect()]);

            foreach ($teamChunks as $chunkIndex => $members) {
                $documentPages->push([
                    'kind' => 'team',
                    'section' => $section,
                    'section_index' => $sectionIndex,
                    'section_type' => $sectionType,
                    'team_members' => $members,
                    'show_intro' => $chunkIndex === 0,
                    'continuation' => $chunkIndex > 0,
                ]);
            }

            continue;
        }

        if (in_array($sectionType, ['agreement', 'acceptance'], true)) {
            $bodyChunks = collect($splitText($section->body ?? '', 1250));
            $lastBodyChunk = max(0, $bodyChunks->count() - 1);

            foreach ($bodyChunks as $chunkIndex => $body) {
                $documentPages->push([
                    'kind' => $sectionType,
                    'section' => $section,
                    'section_index' => $sectionIndex,
                    'section_type' => $sectionType,
                    'body' => $body,
                    'show_signature' => $chunkIndex === $lastBodyChunk,
                    'continuation' => $chunkIndex > 0,
                ]);
            }

            continue;
        }

        $bodyChunks = collect($splitText($section->body ?? ''));

        foreach ($bodyChunks as $chunkIndex => $body) {
            $documentPages->push([
                'kind' => $sectionType,
                'section' => $section,
                'section_index' => $sectionIndex,
                'section_type' => $sectionType,
                'body' => $body,
                'show_aside' => $chunkIndex === 0,
                'continuation' => $chunkIndex > 0,
            ]);
        }
    }

    $logoSrc = $assetSrc($proposal->logo_path)
        ?: DocumentBranding::logoSource(config('luxury-quotes.brand.logo_path'));
    $coverSrc = $assetSrc($proposal->cover_image_path);
    $companyInitials = collect(explode(' ', $proposal->company_name))
        ->filter()
        ->take(2)
        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');
    $companyInitials = $companyInitials !== '' ? $companyInitials : 'PR';
    $money = fn ($amount) => trim(($proposal->currency ?: 'USD').' '.number_format((float) $amount, 2));
    $moneyHtml = fn ($amount) => str_replace(' ', '&nbsp;', e($money($amount)));
    $pageNumbering = (bool) ($settings?->page_numbering ?? true);
    $watermark = $settings?->watermark;
    $preparedFor = $proposal->client_company ?: $proposal->client_name ?: 'Client';
    $preparedBy = $proposal->prepared_by ?: $proposal->company_name;
    $contactItems = collect([$proposal->contact_email, $proposal->phone_number, $proposal->website])->filter()->values();
@endphp

<div class="proposal-document proposal-document--{{ $proposal->theme_key }}">
    @foreach ($documentPages as $pageIndex => $page)
        @php
            $section = $page['section'];
            $sectionType = $page['section_type'];
            $pageNumber = $pageIndex + 1;
            $isContinuation = (bool) ($page['continuation'] ?? false);
        @endphp

        @if ($page['kind'] === 'cover')
            <section class="proposal-page proposal-page--cover">
                <div class="proposal-cover-layout">
                    <div class="proposal-cover-main">
                        <div class="proposal-brand-lockup">
                            @if ($logoSrc)
                                <img class="proposal-logo proposal-image" src="{{ $logoSrc }}" alt="{{ $proposal->company_name }} logo"
                                    style="width:96px;height:36px;object-fit:contain;display:table-cell;vertical-align:middle;">
                            @else
                                <span class="proposal-logo-mark">{{ $companyInitials }}</span>
                            @endif
                            <span class="proposal-brand-copy">
                                <strong>{{ $proposal->company_name }}</strong>
                                <span>{{ $proposal->company_slogan ?: 'Business Proposal' }}</span>
                            </span>
                        </div>

                        <span class="proposal-cover-year">{{ optional($proposal->proposal_date)->format('Y') ?: now()->format('Y') }}</span>

                        <div class="proposal-cover-title">
                            <h1>
                                <span>{{ Str::beforeLast($proposal->title, ' ') ?: 'Business' }}</span>
                                {{ Str::afterLast($proposal->title, ' ') ?: 'Proposal' }}
                            </h1>
                        </div>

                        <div class="proposal-cover-meta">
                            <div class="proposal-cover-meta-item">
                                <span>Prepared For</span>
                                <strong>{{ $preparedFor }}</strong>
                                @if ($proposal->client_name)
                                    <small>{{ $proposal->client_name }}</small>
                                @endif
                            </div>
                            <div class="proposal-cover-meta-item">
                                <span>Prepared By</span>
                                <strong>{{ $preparedBy }}</strong>
                                <small>{{ $proposal->company_name }}</small>
                            </div>
                            <div class="proposal-cover-meta-item">
                                <span>Reference</span>
                                <strong>{{ $proposal->reference_number ?: $proposal->proposal_number }}</strong>
                                <small>{{ optional($proposal->proposal_date)->format('d M Y') ?: now()->format('d M Y') }}</small>
                            </div>
                        </div>

                        @if ($contactItems->isNotEmpty())
                            <div class="proposal-cover-contact">
                                @foreach ($contactItems as $contactItem)
                                    <span>{{ $contactItem }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="proposal-cover-media">
                        <div class="proposal-cover-image {{ $coverSrc ? '' : 'proposal-cover-image--placeholder' }}">
                            @if ($coverSrc)
                                <img class="proposal-image" src="{{ $coverSrc }}" alt="Proposal cover image">
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @else
            <section class="proposal-page proposal-page--{{ $sectionType }} {{ $isContinuation ? 'proposal-page--continuation' : '' }}">
                @if ($watermark)
                    <div class="proposal-watermark">{{ $watermark }}</div>
                @endif

                <header class="proposal-page-header">
                    <span class="proposal-page-kicker">{{ $proposal->company_name }} / {{ $proposal->proposal_number }}</span>
                    @if ($pageNumbering)
                        <span class="proposal-page-number">Page {{ str_pad((string) $pageNumber, 2, '0', STR_PAD_LEFT) }}</span>
                    @endif
                </header>

                @switch($page['kind'])
                    @case('table_of_contents')
                        <span class="proposal-section-eyebrow">{{ $section->eyebrow }}</span>
                        <h2 class="proposal-section-title">{{ $section->title }}</h2>
                        @php
                            $tocItems = $visibleSections->values()->map(fn ($tocSection, $tocIndex) => [
                                'number' => str_pad((string) ($tocIndex + 1), 2, '0', STR_PAD_LEFT),
                                'title' => $tocSection->title,
                            ]);
                            $tocColumns = $tocItems->chunk(max(1, (int) ceil($tocItems->count() / 2)));
                        @endphp
                        <div class="proposal-toc-columns">
                            @foreach ($tocColumns as $tocColumnIndex => $tocColumn)
                                <div class="proposal-toc-column {{ $tocColumnIndex === 1 ? 'proposal-toc-column--right' : '' }}">
                                    <ol class="proposal-toc-list">
                                        @foreach ($tocColumn as $tocItem)
                                            <li>
                                                <span class="proposal-toc-number">{{ $tocItem['number'] }}</span>
                                                <span class="proposal-toc-title">{{ $tocItem['title'] }}</span>
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endforeach
                        </div>
                    @break

                    @case('pricing')
                        <span class="proposal-section-eyebrow">{{ $section->eyebrow }}</span>
                        <h2 class="proposal-section-title">{{ $section->title }}</h2>
                        @if (($page['show_intro'] ?? false) && filled($section->body))
                            <p class="proposal-section-body">{{ $section->body }}</p>
                        @endif

                        <table class="proposal-table">
                            <thead>
                                <tr>
                                    <th class="proposal-col-package">Package</th>
                                    <th class="proposal-col-service">Service</th>
                                    <th class="proposal-col-qty">Qty</th>
                                    <th class="proposal-col-total amount">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($page['pricing_items'] as $item)
                                    <tr>
                                        <td class="proposal-col-package">{{ $item->package }}</td>
                                        <td class="proposal-col-service">
                                            <strong>{{ $item->service_name }}</strong>
                                            <span class="proposal-service-description">{{ $item->description }}</span>
                                        </td>
                                        <td class="proposal-col-qty">{{ number_format((float) $item->quantity, 2) }}</td>
                                        <td class="proposal-col-total amount">{!! $moneyHtml($item->line_total) !!}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if ($page['show_totals'] ?? false)
                            <table class="proposal-total-table">
                                <tr>
                                    <td>Subtotal</td>
                                    <td>{!! $moneyHtml($proposal->subtotal) !!}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td>{!! $moneyHtml($proposal->discount_total) !!}</td>
                                </tr>
                                <tr>
                                    <td>Tax / VAT</td>
                                    <td>{!! $moneyHtml($proposal->tax_total) !!}</td>
                                </tr>
                                <tr>
                                    <td>Grand Total</td>
                                    <td>{!! $moneyHtml($proposal->grand_total) !!}</td>
                                </tr>
                            </table>
                        @endif
                    @break

                    @case('timeline')
                        <span class="proposal-section-eyebrow">{{ $section->eyebrow }}</span>
                        <h2 class="proposal-section-title">{{ $section->title }}</h2>
                        @if (($page['show_intro'] ?? false) && filled($section->body))
                            <p class="proposal-section-body">{{ $section->body }}</p>
                        @endif

                        <div class="proposal-timeline">
                            @foreach ($page['timeline_items'] as $timelineIndex => $timeline)
                                <article class="proposal-timeline-item">
                                    <span class="proposal-timeline-index">{{ ($page['timeline_offset'] ?? 0) + $timelineIndex + 1 }}</span>
                                    <h3>{{ $timeline->phase_title }}</h3>
                                    <p>{{ $timeline->description }}</p>
                                    <div class="proposal-timeline-meta">
                                        {{ $timeline->duration ?: trim(optional($timeline->start_date)->format('M d').' - '.optional($timeline->end_date)->format('M d')) }}
                                        / {{ $timeline->status }}
                                    </div>
                                    @if ($timeline->deliverables)
                                        <p style="margin-top:8px;"><strong>Deliverables:</strong> {{ $timeline->deliverables }}</p>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @break

                    @case('team')
                        <span class="proposal-section-eyebrow">{{ $section->eyebrow }}</span>
                        <h2 class="proposal-section-title">{{ $section->title }}</h2>
                        @if (($page['show_intro'] ?? false) && filled($section->body))
                            <p class="proposal-section-body">{{ $section->body }}</p>
                        @endif

                        <div class="proposal-team-grid">
                            @foreach ($page['team_members'] as $member)
                                <article class="proposal-team-card">
                                    <div class="proposal-team-avatar">{{ Str::upper(Str::substr($member->name, 0, 1)) }}</div>
                                    <h3>{{ $member->name }}</h3>
                                    <span>{{ $member->role }}</span>
                                    <p>{{ $member->bio }}</p>
                                </article>
                            @endforeach
                        </div>
                    @break

                    @case('vision_mission')
                        <span class="proposal-section-eyebrow">{{ $section->eyebrow }}</span>
                        <h2 class="proposal-section-title">{{ $section->title }}</h2>
                        @if (filled($page['body'] ?? null))
                            <p class="proposal-section-body">{{ $page['body'] }}</p>
                        @endif

                        <div class="proposal-split-grid">
                            <article class="proposal-split-card">
                                <span>Mission</span>
                                <h3>Execute with clarity</h3>
                                <p>We focus on clear thinking, careful craft, and reliable delivery so each project creates practical business value.</p>
                            </article>
                            <article class="proposal-split-card">
                                <span>Vision</span>
                                <h3>Build lasting confidence</h3>
                                <p>We want every proposal, product, and client experience to make the organization feel sharper, more credible, and easier to trust.</p>
                            </article>
                        </div>
                    @break

                    @case('agreement')
                    @case('acceptance')
                        <span class="proposal-section-eyebrow">{{ $section->eyebrow }}</span>
                        <h2 class="proposal-section-title">{{ $section->title }}</h2>
                        @if (filled($page['body'] ?? null))
                            <p class="proposal-section-body">{{ $page['body'] }}</p>
                        @endif

                        @if ($page['show_signature'] ?? false)
                            <div class="proposal-signature-grid">
                                <div class="proposal-signature">
                                    <span class="proposal-signature-label">Client Signature</span>
                                    <strong>{{ $preparedFor }}</strong>
                                </div>
                                <div class="proposal-signature">
                                    <span class="proposal-signature-label">Prepared By</span>
                                    <strong>{{ $preparedBy }}</strong>
                                </div>
                                <div class="proposal-signature">
                                    <span class="proposal-signature-label">Date</span>
                                    <strong>{{ optional($proposal->proposal_date)->format('d M Y') ?: now()->format('d M Y') }}</strong>
                                </div>
                            </div>
                        @endif
                    @break

                    @default
                        <div class="proposal-section-grid">
                            <div class="proposal-section-main">
                                <span class="proposal-section-eyebrow">{{ $section->eyebrow }}</span>
                                <h2 class="proposal-section-title">{{ $section->title }}</h2>
                                @if (filled($page['body'] ?? null))
                                    <p class="proposal-section-body">{{ $page['body'] }}</p>
                                @endif
                            </div>
                            @if ($page['show_aside'] ?? false)
                                <aside class="proposal-section-aside">
                                    <div class="proposal-aside-panel">
                                        <strong>{{ str_pad((string) $pageNumber, 2, '0', STR_PAD_LEFT) }}</strong>
                                        <p>{{ $proposal->client_company ?: $proposal->client_name ?: 'Client-ready proposal page' }}</p>
                                    </div>
                                </aside>
                            @endif
                        </div>

                        @if ($page['show_aside'] ?? false)
                            <div class="proposal-stat-grid">
                                <div class="proposal-stat">
                                    <span>Focus</span>
                                    <strong>01</strong>
                                    <p>Clear direction and decision-ready structure.</p>
                                </div>
                                <div class="proposal-stat">
                                    <span>Value</span>
                                    <strong>02</strong>
                                    <p>Premium execution aligned with business outcomes.</p>
                                </div>
                                <div class="proposal-stat">
                                    <span>Delivery</span>
                                    <strong>03</strong>
                                    <p>Practical milestones, review points, and handoff.</p>
                                </div>
                            </div>
                        @endif
                @endswitch

                <footer class="proposal-footer">
                    <span>
                        {{ $proposal->company_name }}
                        @if ($proposal->contact_email)
                            / {{ $proposal->contact_email }}
                        @endif
                        @if ($proposal->website)
                            / {{ $proposal->website }}
                        @endif
                    </span>
                </footer>
            </section>
        @endif
    @endforeach
</div>
