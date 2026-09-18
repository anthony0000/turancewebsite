@php
    use App\Support\DocumentBranding;
    $brandLogo = DocumentBranding::logoSource($brand['logo_path'] ?? null);
    $symbol = $invoice->currencySymbol();
    $percent = rtrim(rtrim(number_format((float) $invoice->completion_percentage, 2, '.', ''), '0'), '.');
@endphp
<article class="payment-document">
    @if ($invoice->isPaid())<div class="pd-paid-stamp">Paid</div>@endif
    <table class="pd-header"><tr>
        <td>
            <div class="pd-invoice-title">INVOICE</div>
            <div class="pd-number"># {{ $invoice->invoice_number }}</div>
            <div class="pd-payment-label">{{ $percent }}% project payment request</div>
        </td>
        <td class="pd-brand">
            @if ($brandLogo)<img class="pd-brand-logo" src="{{ $brandLogo }}" alt="{{ $brand['studio_name'] ?? 'Turance Technologies' }} logo">@endif
            <div class="pd-brand-name">{{ $brand['studio_name'] ?? 'Turance Technologies' }}</div>
            <div class="pd-brand-tagline">{{ $brand['tagline'] ?? 'Excellence Delivered' }}</div>
            <div class="pd-brand-line"></div>
            <div class="pd-brand-contact">RC No. {{ $brand['rc_number'] ?? '3646478' }} &nbsp; | &nbsp; {{ $brand['contact_phone'] ?? '+2348061209440' }}<br>{{ $brand['contact_email'] ?? 'support@turancetechnologies.com' }}</div>
        </td>
    </tr></table>

    <table class="pd-overview"><tr>
        <td class="client">
            <div class="pd-label">Client</div>
            <div class="pd-client-company">{{ $invoice->client_company }}</div>
            @if ($invoice->client_name)<div class="pd-client-line"><strong>{{ $invoice->client_name }}</strong></div>@endif
            @if ($invoice->client_title)<div class="pd-client-line">{{ $invoice->client_title }}</div>@endif
            @if ($invoice->client_email)<div class="pd-client-line">{{ $invoice->client_email }}</div>@endif
            @if ($invoice->client_phone)<div class="pd-client-line">{{ $invoice->client_phone }}</div>@endif
        </td>
        <td class="dates">
            <div class="pd-label">Date</div><div class="pd-date-value">{{ $invoice->issue_date->format('d M Y') }}</div>
            <div class="pd-label">Payment terms</div><div class="pd-date-value">{{ $invoice->payment_terms }}</div>
            @if ($invoice->original_invoice_number)<div class="pd-label">Original invoice</div><div class="pd-date-value">{{ $invoice->original_invoice_number }}</div>@endif
        </td>
        <td class="amount">
            <div class="pd-amount-box">{{ $symbol }}{{ number_format((float) $invoice->total_amount, 2) }}</div>
            <div class="pd-local-amount">{{ $invoice->local_currency }} {{ number_format($invoice->local_total_amount, 0) }}</div>
            <div class="pd-progress-due">{{ $percent }}% payment due</div>
        </td>
    </tr></table>

    <section class="pd-project">
        <div class="pd-kicker">{{ $invoice->project_category ?: 'Project delivery' }}</div>
        <h1>{{ $invoice->project_title }}</h1>
        <p>{{ $invoice->description }}</p>
    </section>

    <table class="pd-line-items"><thead><tr><th>No</th><th>Payment description</th><th>Basis</th><th>Total</th></tr></thead><tbody><tr>
        <td>01</td>
        <td><div class="pd-item-title">{{ $invoice->payment_description }}</div><div class="pd-item-note">{{ $invoice->project?->project_number }} · {{ $invoice->project_title }}</div></td>
        <td>{{ $percent }}% of {{ $symbol }}{{ number_format((float) $invoice->contract_value, 2) }}</td>
        <td>{{ $symbol }}{{ number_format((float) $invoice->subtotal_amount, 2) }}</td>
    </tr></tbody></table>

    <table class="pd-lower"><tr>
        <td>
            <div class="pd-section-title">Payment information</div>
            <p class="pd-payment-intro">Please remit the amount due to the account below and quote this invoice number.</p>
            <table class="pd-bank-table">
                @if ($invoice->bank_name)<tr><td>Bank</td><td>{{ $invoice->bank_name }}</td></tr>@endif
                @if ($invoice->account_name)<tr><td>Account name</td><td>{{ $invoice->account_name }}</td></tr>@endif
                @if ($invoice->account_number)<tr><td>Account number</td><td style="color:#b98000">{{ $invoice->account_number }}</td></tr>@endif
            </table>
        </td>
        <td>
            <table class="pd-totals">
                <tr><td>Original contract value</td><td>{{ $symbol }}{{ number_format((float) $invoice->contract_value, 2) }}</td></tr>
                <tr><td>Payment requested</td><td>{{ $percent }}%</td></tr>
                <tr><td>Subtotal</td><td>{{ $symbol }}{{ number_format((float) $invoice->subtotal_amount, 2) }}</td></tr>
                <tr><td>Tax / VAT ({{ rtrim(rtrim(number_format((float) $invoice->tax_rate, 2, '.', ''), '0'), '.') }}%)</td><td>{{ $symbol }}{{ number_format((float) $invoice->tax_amount, 2) }}</td></tr>
                <tr class="total"><td>Total due</td><td>{{ $symbol }}{{ number_format((float) $invoice->total_amount, 2) }}</td></tr>
                <tr><td>{{ $invoice->local_currency }} equivalent</td><td>{{ $invoice->local_currency }} {{ number_format($invoice->local_total_amount, 0) }}</td></tr>
                <tr><td>Exchange rate</td><td>{{ $symbol }}1 = {{ $invoice->local_currency }} {{ number_format((float) $invoice->exchange_rate, 2) }}</td></tr>
            </table>
        </td>
    </tr></table>

    <div class="pd-balance"><table><tr><td><div class="pd-label" style="margin:0">Balance after this payment</div><strong>{{ $symbol }}{{ number_format($invoice->balance_amount, 2) }} / {{ $invoice->local_currency }} {{ number_format($invoice->local_balance_amount, 0) }}</strong></td><td>{{ $invoice->notes ?: 'Thank you for your continued partnership.' }}</td></tr></table></div>

    <table class="pd-footer"><tr>
        <td><span>Phone</span><strong>{{ $brand['contact_phone'] ?? '+2348061209440' }}</strong></td>
        <td><span>Email</span><strong>{{ $brand['contact_email'] ?? 'support@turancetechnologies.com' }}</strong></td>
        <td><span>Prepared by</span><strong>{{ $invoice->prepared_by ?: ($brand['studio_name'] ?? 'Turance Technologies') }}</strong></td>
    </tr></table>
</article>
