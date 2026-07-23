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

    $scopeItems = $storedLineItems->isNotEmpty()
        ? $storedLineItems->pluck('description')->values()
        : collect($quote->scope_items ?? [])->filter(fn ($item) => filled($item))->values();

    if ($scopeItems->isEmpty()) {
        $scopeItems = collect(['Project scope to be confirmed during final approval.']);
    }

    if ($storedLineItems->isNotEmpty()) {
        $lineItems = $storedLineItems
            ->map(fn (array $item, int $index): array => [
                'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'description' => $item['description'],
                'amount' => '$'.number_format($item['amount'], 0),
            ])
            ->all();
    } else {
        $investmentValue = (float) $quote->investment_amount;
        $lineCount = max($scopeItems->count(), 1);
        $allocatedAmount = 0.0;
        $baseLineAmount = floor(($investmentValue / $lineCount) * 100) / 100;

        $lineItems = $scopeItems
            ->map(function ($item, int $index) use (&$allocatedAmount, $baseLineAmount, $investmentValue, $lineCount): array {
                $amount = $index === $lineCount - 1
                    ? round($investmentValue - $allocatedAmount, 2)
                    : $baseLineAmount;

                $allocatedAmount += $amount;

                return [
                    'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'description' => (string) $item,
                    'amount' => '$'.number_format($amount, 0),
                ];
            })
            ->all();
    }

    $investmentValue = $storedLineItems->isNotEmpty()
        ? (float) $storedLineItems->sum('amount')
        : (float) $quote->investment_amount;
    $investment = '$'.number_format($investmentValue, 0);
    $exchangeRate = max(1, (float) ($quote->exchange_rate ?? 1370));
    $nairaInvestment = 'NGN '.number_format($investmentValue * $exchangeRate, 0);
    $exchangeRateLabel = '$1 = NGN '.number_format($exchangeRate, 2);
    $brandName = $brand['studio_name'] ?? 'Turance Technologies';
    $brandLogoSrc = DocumentBranding::logoSource($brand['logo_path'] ?? null);
    $brandEmail = $brand['contact_email'] ?? 'support@turancetechnologies.com';
    $brandPhone = $brand['contact_phone'] ?? '+2349124948602';
    $brandWebsite = $brand['website'] ?? config('app.url');
    $clientSigner = $quote->recipient_name ?: 'Authorized Client Representative';
    $clientTitle = $quote->recipient_title ?: ($quote->company_industry ?: 'Client Representative');
    $outcomes = collect($quote->outcomes ?? [])->filter(fn ($item) => filled($item))->values();
    $milestones = collect($quote->milestones ?? [])->filter(fn ($item) => filled($item))->values();
    $optionalAddons = collect($quote->optional_addons ?? [])->filter(fn ($item) => filled($item))->values();
    $preparedDate = optional($quote->created_at)->format('F d, Y') ?: now()->format('F d, Y');
    $validUntil = optional($quote->valid_until)->format('F d, Y') ?: 'To be confirmed';
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>MOU {{ $mouNumber }}</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #20242c;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.5;
        }

        * {
            box-sizing: border-box;
        }

        .mou-document {
            width: 100%;
            border: 1px solid #d8d2c4;
            background: #ffffff;
        }

        .mou-header {
            padding: 30px 36px 22px;
            background: #20242c;
            color: #ffffff;
        }

        .mou-header-table,
        .mou-meta-table,
        .mou-party-grid,
        .mou-scope-table,
        .mou-two-col-table,
        .mou-signature-table,
        .mou-footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mou-header-left {
            width: 62%;
            vertical-align: top;
        }

        .mou-header-right {
            width: 38%;
            text-align: right;
            vertical-align: top;
        }

        .mou-brand-logo {
            display: inline-block;
            width: 122px;
            height: 32px;
            margin: 0 0 9px auto;
            object-fit: contain;
            object-position: right center;
        }

        .mou-kicker,
        .mou-ref,
        .mou-label,
        .mou-section-kicker,
        .mou-scope-table th,
        .mou-signature-label,
        .mou-footer-label {
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .mou-kicker {
            display: block;
            color: #d4af37;
            font-size: 9px;
            font-weight: 700;
        }

        h1 {
            margin: 8px 0 0;
            font-size: 27px;
            line-height: 1.1;
            font-weight: 800;
        }

        .mou-ref {
            display: block;
            margin-top: 8px;
            color: #d6dbe4;
            font-size: 9px;
        }

        .mou-brand {
            display: block;
            color: #ffffff;
            font-size: 13px;
            line-height: 1.3;
            font-weight: 800;
        }

        .mou-brand-line {
            display: block;
            margin-top: 3px;
            color: #d6dbe4;
            font-size: 8.5px;
        }

        .mou-body {
            padding: 28px 36px 26px;
        }

        .mou-intro {
            margin: 0 0 18px;
            color: #4f5865;
            font-size: 10.5px;
        }

        .mou-meta-table {
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .mou-meta-table td {
            width: 33.333%;
            padding: 11px 12px;
            border: 1px solid #e3e1db;
            vertical-align: top;
        }

        .mou-label {
            display: block;
            color: #7a818c;
            font-size: 7.8px;
            font-weight: 800;
        }

        .mou-meta-table strong,
        .mou-party-card strong {
            display: block;
            margin-top: 5px;
            color: #141b25;
            font-size: 10.5px;
            line-height: 1.35;
        }

        .mou-party-grid {
            margin-bottom: 19px;
        }

        .mou-party-card {
            width: 50%;
            padding: 14px;
            border: 1px solid #e3e1db;
            vertical-align: top;
        }

        .mou-party-card:first-child {
            border-right: 0;
        }

        .mou-party-card span {
            display: block;
            color: #5f6672;
            font-size: 9px;
            line-height: 1.4;
        }

        .mou-section {
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .mou-section-kicker {
            display: block;
            margin-bottom: 4px;
            color: #a57714;
            font-size: 8px;
            font-weight: 800;
        }

        h2 {
            margin: 0 0 8px;
            color: #141b25;
            font-size: 14px;
            line-height: 1.25;
        }

        .mou-section p {
            margin: 0;
            color: #4f5865;
        }

        .mou-scope-table {
            margin-top: 9px;
            table-layout: fixed;
        }

        .mou-scope-table th {
            padding: 8px;
            border-bottom: 1px solid #d7dbe2;
            color: #7a818c;
            font-size: 8px;
            text-align: left;
        }

        .mou-scope-table th:last-child,
        .mou-scope-table td:last-child {
            text-align: right;
        }

        .mou-scope-table td {
            padding: 8px;
            border-bottom: 1px solid #edf0f4;
            color: #343b48;
            vertical-align: top;
        }

        .mou-scope-table td:first-child {
            width: 12%;
            color: #7a818c;
        }

        .mou-scope-table td:nth-child(2) {
            width: 62%;
        }

        .mou-scope-table td:last-child {
            width: 26%;
            color: #141b25;
            font-weight: 800;
        }

        .mou-two-col-table {
            margin-top: 10px;
        }

        .mou-two-col-table td {
            width: 50%;
            padding: 12px 14px;
            border: 1px solid #e3e1db;
            vertical-align: top;
        }

        .mou-two-col-table td:first-child {
            border-right: 0;
        }

        .mou-list {
            margin: 0;
            padding-left: 14px;
        }

        .mou-list li {
            margin-bottom: 4px;
            color: #4f5865;
        }

        .mou-terms-list {
            margin: 8px 0 0;
            padding-left: 16px;
        }

        .mou-terms-list li {
            margin-bottom: 5px;
            color: #4f5865;
        }

        .mou-commercial-box {
            margin-top: 10px;
            padding: 12px 14px;
            border: 1px solid #d7d2c4;
            background: #fbf8f0;
        }

        .mou-commercial-box strong {
            display: block;
            color: #141b25;
            font-size: 16px;
        }

        .mou-commercial-conversion {
            display: block;
            margin-top: 4px;
            color: #4f5865;
            font-size: 9.5px;
            font-weight: 700;
        }

        .mou-signature-table {
            margin-top: 26px;
        }

        .mou-signature-table td {
            width: 50%;
            padding: 0 18px 0 0;
            vertical-align: top;
        }

        .mou-signature-table td:last-child {
            padding-right: 0;
            padding-left: 18px;
        }

        .mou-signature-line {
            height: 42px;
            border-bottom: 1px solid #20242c;
        }

        .mou-signature-label {
            display: block;
            margin-top: 8px;
            color: #7a818c;
            font-size: 8px;
            font-weight: 800;
        }

        .mou-signature-name {
            display: block;
            margin-top: 3px;
            color: #141b25;
            font-weight: 800;
        }

        .mou-footer-table {
            margin-top: 22px;
            border-top: 1px solid #e1e4e8;
        }

        .mou-footer-table td {
            width: 33.333%;
            padding-top: 9px;
            padding-right: 10px;
            vertical-align: top;
        }

        .mou-footer-label {
            display: block;
            color: #7a818c;
            font-size: 7.8px;
            font-weight: 800;
        }

        .mou-footer-table strong {
            display: block;
            margin-top: 3px;
            color: #20242c;
            font-size: 8.8px;
            line-height: 1.35;
        }
    </style>
</head>

<body>
    <div class="mou-document">
        <div class="mou-header">
            <table class="mou-header-table" role="presentation">
                <tr>
                    <td class="mou-header-left">
                        <span class="mou-kicker">Generated From Saved Invoice</span>
                        <h1>Memorandum of Understanding</h1>
                        <span class="mou-ref">{{ $mouNumber }} / Invoice {{ $quote->quote_number }}</span>
                    </td>
                    <td class="mou-header-right">
                        @if ($brandLogoSrc)
                            <img class="mou-brand-logo" src="{{ $brandLogoSrc }}" alt="{{ $brandName }} logo">
                        @endif
                        <strong class="mou-brand">{{ $brandName }}</strong>
                        <span class="mou-brand-line">{{ $brand['tagline'] ?? 'Excellence Delivered' }}</span>
                        <span class="mou-brand-line">{{ $brandEmail }}</span>
                        <span class="mou-brand-line">{{ $brandPhone }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="mou-body">
            <p class="mou-intro">
                This Memorandum of Understanding records the working agreement for the engagement described in invoice
                {{ $quote->quote_number }}. It is generated only after the invoice record has been created, and it uses the
                saved invoice details as the commercial and delivery reference for the contract file.
            </p>

            <table class="mou-meta-table" role="presentation">
                <tr>
                    <td>
                        <span class="mou-label">Project</span>
                        <strong>{{ $quote->project_title }}</strong>
                    </td>
                    <td>
                        <span class="mou-label">Category</span>
                        <strong>{{ $quote->project_category }}</strong>
                    </td>
                    <td>
                        <span class="mou-label">Prepared Date</span>
                        <strong>{{ $preparedDate }}</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="mou-label">Invoice Reference</span>
                        <strong>{{ $quote->quote_number }}</strong>
                    </td>
                    <td>
                        <span class="mou-label">Timeline</span>
                        <strong>{{ $quote->timeline }}</strong>
                    </td>
                    <td>
                        <span class="mou-label">Invoice Valid Until</span>
                        <strong>{{ $validUntil }}</strong>
                    </td>
                </tr>
            </table>

            <table class="mou-party-grid" role="presentation">
                <tr>
                    <td class="mou-party-card">
                        <span class="mou-label">Service Provider</span>
                        <strong>{{ $brandName }}</strong>
                        <span>{{ $brandEmail }}</span>
                        <span>{{ $brandPhone }}</span>
                        <span>{{ $brandWebsite }}</span>
                    </td>
                    <td class="mou-party-card">
                        <span class="mou-label">Client</span>
                        <strong>{{ $quote->company_name }}</strong>
                        @if (filled($quote->company_industry))
                            <span>{{ $quote->company_industry }}</span>
                        @endif
                        @if (filled($quote->recipient_name))
                            <span>{{ $quote->recipient_name }}{{ filled($quote->recipient_title) ? ', '.$quote->recipient_title : '' }}</span>
                        @endif
                        @if (filled($quote->recipient_email))
                            <span>{{ $quote->recipient_email }}</span>
                        @endif
                        @if (filled($quote->recipient_phone))
                            <span>{{ $quote->recipient_phone }}</span>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="mou-section">
                <span class="mou-section-kicker">01 / Purpose</span>
                <h2>Shared Understanding</h2>
                <div>{!! $renderRichText($quote->executive_summary) !!}</div>
            </div>

            <div class="mou-section">
                <span class="mou-section-kicker">02 / Scope</span>
                <h2>Services Covered By This MOU</h2>
                <table class="mou-scope-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Service Description</th>
                            <th>Commercial Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lineItems as $item)
                            <tr>
                                <td>{{ $item['number'] }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td>{{ $item['amount'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mou-section">
                <span class="mou-section-kicker">03 / Commercial Terms</span>
                <h2>Invoice-Based Financial Understanding</h2>
                <p>
                    The parties acknowledge that invoice {{ $quote->quote_number }} is the commercial reference for this
                    engagement. Payment timing, deposit handling, and final delivery access should follow the agreed
                    payment process connected to that invoice.
                </p>
                <div class="mou-commercial-box">
                    <span class="mou-label">Total Contract Value</span>
                    <strong>{{ $investment }}</strong>
                    <span class="mou-commercial-conversion">{{ $nairaInvestment }} at {{ $exchangeRateLabel }}</span>
                </div>
            </div>

            <div class="mou-section">
                <span class="mou-section-kicker">04 / Delivery</span>
                <h2>Milestones And Intended Outcomes</h2>
                <table class="mou-two-col-table" role="presentation">
                    <tr>
                        <td>
                            <span class="mou-label">Delivery Milestones</span>
                            <ul class="mou-list">
                                @forelse ($milestones as $item)
                                    <li>{!! $renderRichInline($item) !!}</li>
                                @empty
                                    <li>Milestones will be scheduled immediately after approval.</li>
                                @endforelse
                            </ul>
                        </td>
                        <td>
                            <span class="mou-label">Expected Outcomes</span>
                            <ul class="mou-list">
                                @forelse ($outcomes as $item)
                                    <li>{!! $renderRichInline($item) !!}</li>
                                @empty
                                    <li>Project outcomes will be aligned during the approval stage.</li>
                                @endforelse
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="mou-section">
                <span class="mou-section-kicker">05 / Working Terms</span>
                <h2>Responsibilities And Change Control</h2>
                <ul class="mou-terms-list">
                    <li>The client will provide timely access, approvals, content, brand assets, and feedback required to keep the stated timeline moving.</li>
                    <li>{{ $brandName }} will deliver the approved scope with professional care, structured communication, and reasonable quality assurance before handoff.</li>
                    <li>Any service, revision, integration, or deliverable outside the saved invoice scope should be confirmed in writing and may require a revised invoice.</li>
                    <li>Confidential business information shared by either party should be treated as private and used only for the engagement.</li>
                    <li>This MOU supports the contract file and may be replaced by a fuller service agreement where either party requests more detailed legal terms.</li>
                </ul>
            </div>

            @if ($optionalAddons->isNotEmpty())
                <div class="mou-section">
                    <span class="mou-section-kicker">06 / Optional Extensions</span>
                    <h2>Add-Ons Available By Separate Approval</h2>
                    <ul class="mou-terms-list">
                        @foreach ($optionalAddons as $item)
                            <li>{!! $renderRichInline($item) !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="mou-signature-table" role="presentation">
                <tr>
                    <td>
                        <div class="mou-signature-line"></div>
                        <span class="mou-signature-label">For {{ $brandName }}</span>
                        <span class="mou-signature-name">Authorized Representative</span>
                        <span>Date: ________________________</span>
                    </td>
                    <td>
                        <div class="mou-signature-line"></div>
                        <span class="mou-signature-label">For {{ $quote->company_name }}</span>
                        <span class="mou-signature-name">{{ $clientSigner }} / {{ $clientTitle }}</span>
                        <span>Date: ________________________</span>
                    </td>
                </tr>
            </table>

            <table class="mou-footer-table" role="presentation">
                <tr>
                    <td>
                        <span class="mou-footer-label">MOU Reference</span>
                        <strong>{{ $mouNumber }}</strong>
                    </td>
                    <td>
                        <span class="mou-footer-label">Generated From</span>
                        <strong>Invoice {{ $quote->quote_number }}</strong>
                    </td>
                    <td>
                        <span class="mou-footer-label">Prepared By</span>
                        <strong>{{ $brandName }}</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
