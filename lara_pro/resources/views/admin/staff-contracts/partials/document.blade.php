@php
    use App\Support\DocumentBranding;

    $brandLogoSrc = DocumentBranding::logoSource($brand['logo_path'] ?? null);
    $brandRcNumber = $brand['rc_number'] ?? '3646478';
    $period = collect([
        optional($contract->starts_on)->format('M d, Y'),
        optional($contract->ends_on)->format('M d, Y'),
    ])->filter()->implode(' - ');
    $projectPeriod = collect([
        optional($contract->project->starts_on)->format('M d, Y'),
        optional($contract->project->ends_on)->format('M d, Y'),
    ])->filter()->implode(' - ');
@endphp

<div class="staff-contract-document">
    <header class="staff-contract-header">
        <table class="staff-contract-header-table">
            <tr>
                <td class="staff-contract-brand">
                    @if ($brandLogoSrc)
                        <img src="{{ $brandLogoSrc }}" alt="{{ $brand['studio_name'] ?? 'Turance Technologies' }} logo">
                    @endif
                    <strong>{{ $brand['studio_name'] ?? 'Turance Technologies' }}</strong>
                    <span>{{ $brand['tagline'] ?? 'Excellence Delivered' }}</span>
                    <span>RC No. {{ $brandRcNumber }}</span>
                </td>
                <td class="staff-contract-header-meta">
                    <span>Contract document</span>
                    <strong>{{ $contract->contract_number }}</strong>
                    <span style="margin-top: 12px;">{{ ucfirst(str_replace('_', ' ', $contract->status)) }}</span>
                </td>
            </tr>
        </table>

        <h1 class="staff-contract-title">Contract Staff Agreement</h1>
        <p class="staff-contract-subtitle">A project-specific engagement agreement between {{ $contract->company_name }} and {{ $contract->staff_name }}.</p>
    </header>

    <main class="staff-contract-body">
        <section class="staff-contract-project">
            <span class="staff-contract-kicker">Parent project - {{ $contract->project->project_number }}@if ($contract->invoice) / Invoice {{ $contract->invoice->quote_number }}@endif</span>
            <h2>{{ $contract->project->name }}</h2>
            <p>
                @if ($contract->project->client_company)
                    {{ $contract->project->client_company }}
                @endif
                @if ($contract->project->client_name)
                    {{ $contract->project->client_company ? ' - ' : '' }}{{ $contract->project->client_name }}
                @endif
                @if ($projectPeriod)
                    {{ ($contract->project->client_company || $contract->project->client_name) ? ' - ' : '' }}{{ $projectPeriod }}
                @endif
            </p>
        </section>

        <table class="staff-contract-meta-table">
            <tr>
                <td>
                    <span class="staff-contract-meta-label">Staff member</span>
                    <strong class="staff-contract-meta-value">{{ $contract->staff_name }}</strong>
                </td>
                <td>
                    <span class="staff-contract-meta-label">Project role</span>
                    <strong class="staff-contract-meta-value">{{ $contract->staff_role }}</strong>
                </td>
                <td>
                    <span class="staff-contract-meta-label">Engagement period</span>
                    <strong class="staff-contract-meta-value">{{ $period ?: 'To be agreed' }}</strong>
                </td>
                <td>
                    <span class="staff-contract-meta-label">Staff contact</span>
                    <strong class="staff-contract-meta-value">{{ $contract->staff_email ?: ($contract->staff_phone ?: 'Not provided') }}</strong>
                </td>
            </tr>
        </table>

        <section class="staff-contract-section">
            <span class="staff-contract-section-label">01 / Price and payment</span>
            <h2>Agreed compensation</h2>
            <div class="staff-contract-price">{{ $contract->currency }} {{ number_format((float) $contract->agreed_fee, 2) }}</div>
            <p class="staff-contract-price-note">{{ $contract->payment_terms }}</p>
        </section>

        <section class="staff-contract-section">
            <span class="staff-contract-section-label">02 / Scope of work</span>
            <h2>Responsibilities for this project</h2>
            <p class="staff-contract-copy">{{ $contract->scope_of_work }}</p>
        </section>

        <section class="staff-contract-section">
            <span class="staff-contract-section-label">03 / Terms</span>
            <h2>Working terms and conditions</h2>
            <p class="staff-contract-copy">{{ $contract->terms }}</p>
        </section>

        @if ($contract->notes)
            <section class="staff-contract-section">
                <span class="staff-contract-section-label">Internal record</span>
                <h2>Notes</h2>
                <p class="staff-contract-note">{{ $contract->notes }}</p>
            </section>
        @endif

        <section class="staff-contract-section staff-contract-signatures">
            <span class="staff-contract-section-label">04 / Signing section</span>
            <h2>Acceptance and signatures</h2>
            <p class="staff-contract-copy">By signing below, both parties confirm that they have reviewed and accepted the project assignment, agreed fee, scope of work, and terms in this contract.</p>
            <table class="staff-contract-signature-table staff-contract-signatures">
                <tr>
                    <td>
                        <div class="staff-contract-signature-line">{{ $contract->company_signatory_name }}</div>
                        <span class="staff-contract-signature-label">For {{ $contract->company_name }}</span>
                        <div class="staff-contract-signature-value">{{ $contract->company_signatory_title ?: 'Authorised signatory' }}</div>
                        <span class="staff-contract-signature-label">Name / title</span>
                        <div class="staff-contract-signature-value">{{ optional($contract->company_signed_date)->format('M d, Y') ?: 'Date pending' }}</div>
                        <span class="staff-contract-signature-label">Signature date</span>
                    </td>
                    <td>
                        <div class="staff-contract-signature-line">{{ $contract->staff_signatory_name ?: $contract->staff_name }}</div>
                        <span class="staff-contract-signature-label">Contract staff</span>
                        <div class="staff-contract-signature-value">{{ $contract->staff_role }}</div>
                        <span class="staff-contract-signature-label">Role</span>
                        <div class="staff-contract-signature-value">{{ optional($contract->staff_signed_date)->format('M d, Y') ?: 'Date pending' }}</div>
                        <span class="staff-contract-signature-label">Signature date</span>
                    </td>
                </tr>
            </table>
        </section>
    </main>

    <footer class="staff-contract-footer">
        <table class="staff-contract-footer-table">
            <tr>
                <td>
                    <span class="staff-contract-footer-label">Document record</span>
                    <p>{{ $contract->contract_number }} - Project {{ $contract->project->project_number }}</p>
                </td>
                <td>
                    <span class="staff-contract-footer-label">Prepared by</span>
                    <p>{{ $contract->company_name }} - RC No. {{ $brandRcNumber }}</p>
                </td>
            </tr>
        </table>
    </footer>
</div>
