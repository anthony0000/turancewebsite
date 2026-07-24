@php
    use App\Support\DocumentBranding;

    $storedLineItems = collect($quote->line_items ?? [])
        ->filter(fn ($item) => is_array($item) && filled($item['description'] ?? null))
        ->map(fn ($item) => [
            'description' => (string) $item['description'],
            'amount' => round((float) ($item['amount'] ?? 0), 2),
        ])
        ->filter(fn ($item) => $item['amount'] > 0)
        ->values();
    $investmentValue = $storedLineItems->isNotEmpty()
        ? (float) $storedLineItems->sum('amount')
        : (float) $quote->investment_amount;
    $investment = '$'.number_format($investmentValue, 0);
    $exchangeRate = max(1, (float) ($quote->exchange_rate ?? 1370));
    $nairaInvestment = 'NGN '.number_format($investmentValue * $exchangeRate, 0);
    $exchangeRateLabel = '$1 = NGN '.number_format($exchangeRate, 2);
    $createdDate = optional($quote->created_at)->format('d M Y');
    $validUntil = optional($quote->valid_until)->format('d M Y');
    $introMessage = filled($quote->intro_message)
        ? $quote->intro_message
        : "Prepared for {$quote->company_name} as a structured invoice for the approved engagement.";
    $projectCopy = filled($quote->executive_summary) ? $quote->executive_summary : $introMessage;
    $closingNote = filled($quote->closing_note)
        ? $quote->closing_note
        : 'Once this invoice is approved, the next step is to confirm scope, lock the timeline, and move into delivery.';
    $recipientName = $quote->recipient_name ?: $quote->company_name;
    $recipientTitle = $quote->recipient_title ?: ($quote->company_industry ?: 'Client');
    $recipientLines = array_filter([
        $quote->company_name,
        $quote->company_industry,
        $quote->recipient_email,
        $quote->recipient_phone,
    ]);
    $scopeItems = $storedLineItems->isNotEmpty()
        ? $storedLineItems->pluck('description')->values()
        : collect($quote->scope_items ?? [])->filter(fn ($item) => filled($item))->values();

    if ($scopeItems->isEmpty()) {
        $scopeItems = collect(['Project scope to be confirmed during final approval.']);
    }

    if ($storedLineItems->isNotEmpty()) {
        $lineItems = $storedLineItems
            ->map(fn (array $item, int $index): array => [
                'qty' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'description' => $item['description'],
                'timeline' => $quote->timeline,
                'amount' => '$'.number_format($item['amount'], 0),
            ])
            ->all();
    } else {
        $lineItems = [];
        $lineCount = max($scopeItems->count(), 1);
        $allocatedAmount = 0.0;
        $baseLineAmount = floor(($investmentValue / $lineCount) * 100) / 100;

        foreach ($scopeItems as $index => $item) {
            $lineAmount = $index === $lineCount - 1
                ? round($investmentValue - $allocatedAmount, 2)
                : $baseLineAmount;

            $allocatedAmount += $lineAmount;

            $lineItems[] = [
                'qty' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'description' => $item,
                'timeline' => $quote->timeline,
                'amount' => '$'.number_format($lineAmount, 0),
            ];
        }
    }

    $brandName = $brand['studio_name'] ?? 'Turance Technologies';
    $brandLogoSrc = DocumentBranding::logoSource($brand['logo_path'] ?? null);
    $brandLines = array_filter([
        $brand['tagline'] ?? null,
        $brand['contact_phone'] ?? null,
        $brand['contact_email'] ?? null,
    ]);
    $outcomes = collect($quote->outcomes ?? [])->filter(fn ($item) => filled($item))->values();
    $milestones = collect($quote->milestones ?? [])->filter(fn ($item) => filled($item))->values();
    $optionalAddons = collect($quote->optional_addons ?? [])->filter(fn ($item) => filled($item))->values();
    $sanitizeRichText = function (?string $value): string {
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
    };
    $renderRichText = function (?string $value) use ($sanitizeRichText): \Illuminate\Support\HtmlString {
        $text = trim((string) $value);

        if ($text === '') {
            return new \Illuminate\Support\HtmlString('');
        }

        if ($text === strip_tags($text)) {
            return new \Illuminate\Support\HtmlString(nl2br(e($text), false));
        }

        return new \Illuminate\Support\HtmlString($sanitizeRichText($text));
    };
    $renderRichInline = function (?string $value) use ($sanitizeRichText): \Illuminate\Support\HtmlString {
        $html = $sanitizeRichText($value);

        if ($html === strip_tags($html)) {
            return new \Illuminate\Support\HtmlString(e($html));
        }

        $html = preg_replace('/<br\s*\/?>/i', ' ', $html) ?? $html;
        $html = preg_replace('/<\/?(p|div|ul|ol|li)>/i', ' ', $html) ?? $html;

        return new \Illuminate\Support\HtmlString(trim($html));
    };
@endphp

<div class="quote-document">
    <div class="quote-header-band">
        <table class="quote-header-table" role="presentation">
            <tr>
                <td class="quote-header-left">
                    <span class="quote-document-type">Invoice</span>
                    <span class="quote-document-ref"># {{ $quote->quote_number }}</span>
                </td>
                <td class="quote-header-right" colspan="2">
                    @if ($brandLogoSrc)
                        <img class="quote-brand-logo" src="{{ $brandLogoSrc }}" alt="{{ $brandName }} logo">
                    @endif
                    <strong class="quote-brand-name">{{ $brandName }}</strong>
                    @foreach ($brandLines as $line)
                        <span class="quote-brand-detail">{{ $line }}</span>
                    @endforeach
                </td>
            </tr>
            <tr class="quote-header-meta">
                <td class="quote-header-left quote-to-cell">
                    <span class="quote-to-label">Client</span>
                    <strong>{{ $recipientName }}</strong>
                    <span class="quote-to-role">{{ $recipientTitle }}</span>

                    @foreach ($recipientLines as $line)
                        <span class="quote-to-detail">{{ $line }}</span>
                    @endforeach
                </td>
                <td class="quote-header-center">
                    <div class="quote-meta-line">
                        <span>Date</span>
                        <strong>{{ $createdDate ?: now()->format('d M Y') }}</strong>
                    </div>
                    <div class="quote-meta-line">
                        <span>Valid Until</span>
                        <strong>{{ $validUntil }}</strong>
                    </div>
                </td>
                <td class="quote-header-right quote-total-cell">
                    <div class="quote-total-box">{{ $investment }}</div>
                    <span class="quote-total-converted">{{ $nairaInvestment }}</span>
                    <span class="quote-total-label">Total Due</span>
                </td>
            </tr>
            <tr class="quote-header-secondary">
                <td class="quote-project-cell" colspan="3">
                    <span class="quote-project-kicker">{{ $quote->project_category }}</span>
                    <strong class="quote-project-title">{{ $quote->project_title }}</strong>
                    <div class="quote-project-copy">{!! $renderRichText($projectCopy) !!}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="quote-body">
        <table class="quote-items-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Service Description</th>
                    <th>Timeline</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lineItems as $item)
                    <tr>
                        <td class="quote-col-qty">{{ $item['qty'] }}</td>
                        <td class="quote-col-description">
                            <strong>{{ $item['description'] }}</strong>
                            <span>{{ $quote->project_title }}</span>
                        </td>
                        <td class="quote-col-time">{{ $item['timeline'] }}</td>
                        <td class="quote-col-amount">{{ $item['amount'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="quote-summary-table" role="presentation">
            <tr>
                <td class="quote-payment-cell">
                    <h3>Payment Information</h3>
                    <div>{!! $renderRichText($closingNote) !!}</div>
                    <table class="quote-payment-meta" role="presentation">
                        <tr>
                            <td>
                                <span>Prepared By</span>
                                <strong>{{ $brandName }}</strong>
                            </td>
                            <td>
                                <span>Contact</span>
                                <strong>{{ $brand['contact_email'] ?? 'hello@turancetechnologies.com' }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="quote-totals-cell">
                    <table class="quote-totals-table" role="presentation">
                        <tr>
                            <td>Net amount</td>
                            <td>{{ $investment }}</td>
                        </tr>
                        <tr>
                            <td>Tax / VAT</td>
                            <td>$0</td>
                        </tr>
                        <tr class="quote-total-final">
                            <td>Total due</td>
                            <td>{{ $investment }}</td>
                        </tr>
                        <tr>
                            <td>Naira equivalent</td>
                            <td>{{ $nairaInvestment }}</td>
                        </tr>
                        <tr>
                            <td>Exchange rate</td>
                            <td>{{ $exchangeRateLabel }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="quote-compact-table" role="presentation">
            <tr>
                <td class="quote-compact-cell">
                    <h3>Expected Outcomes</h3>
                    <ul class="quote-clean-list">
                        @forelse ($outcomes as $item)
                            <li>{!! $renderRichInline($item) !!}</li>
                        @empty
                            <li>Project outcomes will be aligned during the approval stage.</li>
                        @endforelse
                    </ul>
                </td>
                <td class="quote-compact-cell quote-compact-cell-last">
                    <h3>Delivery Milestones</h3>
                    <ul class="quote-clean-list">
                        @forelse ($milestones as $item)
                            <li>{!! $renderRichInline($item) !!}</li>
                        @empty
                            <li>Milestones will be scheduled immediately after approval.</li>
                        @endforelse
                    </ul>
                </td>
            </tr>

            @if ($optionalAddons->isNotEmpty())
                <tr>
                    <td class="quote-compact-cell quote-compact-cell-full" colspan="2">
                        <h3>Optional Add-ons</h3>
                        <ul class="quote-clean-list">
                            @foreach ($optionalAddons as $item)
                                <li>{!! $renderRichInline($item) !!}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endif
        </table>

        <table class="quote-footer-table" role="presentation">
            <tr>
                <td>
                    <span class="quote-footer-label">Phone</span>
                    <strong>{{ $brand['contact_phone'] ?? '+2349124948602' }}</strong>
                </td>
                <td>
                    <span class="quote-footer-label">Email</span>
                    <strong>{{ $brand['contact_email'] ?? 'hello@turancetechnologies.com' }}</strong>
                </td>
                <td>
                    <span class="quote-footer-label">Prepared By</span>
                    <strong>{{ $brandName }}</strong>
                </td>
            </tr>
        </table>
    </div>
</div>
