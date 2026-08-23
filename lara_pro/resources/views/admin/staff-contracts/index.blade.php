@extends('admin.layouts.app')

@section('title', 'Staff Contracts | Admin')

@section('content')
    <style>
        .contract-status {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(184, 134, 11, 0.1);
            color: var(--accent-soft);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .contract-status--signed,
        .contract-status--active {
            background: rgba(47, 128, 84, 0.12);
            color: var(--success);
        }

        .contract-status--terminated {
            background: rgba(185, 74, 61, 0.11);
            color: var(--danger);
        }
    </style>

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Invoice-linked agreements</span>
            <h1>Keep every contract staff engagement clear, priced, and ready to sign.</h1>
            <p>
                Create a formal staff contract from an existing invoice, record the agreed price and terms, and keep
                both signing parties in one exportable document.
            </p>
            <div class="hero-actions">
                <a class="button" href="{{ route('admin.staff-contracts.create') }}">Create Staff Contract</a>
                <a class="ghost-button" href="{{ route('admin.proposals.index') }}">Open Proposal Studio</a>
            </div>
        </div>

        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Invoices available</span>
                <strong>{{ number_format($invoiceCount) }}</strong>
                <p>Existing invoices available to the staff agreement register.</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Agreement value</span>
                <strong>{{ number_format($totalValue, 2) }}</strong>
                <p>{{ number_format($signedCount) }} signed {{ $signedCount === 1 ? 'agreement' : 'agreements' }}. Each contract retains its own currency.</p>
            </div>
        </div>
    </section>

    <section class="kpi-grid" style="margin-top: 24px;">
        <article class="panel kpi-card kpi-card--quotes">
            <span class="metric-label">Total contracts</span>
            <strong class="kpi-value">{{ number_format($contracts->count()) }}</strong>
            <span class="kpi-meta">Every project-bound staff agreement.</span>
        </article>
        <article class="panel kpi-card kpi-card--leads">
            <span class="metric-label">In signature flow</span>
            <strong class="kpi-value">{{ number_format($activeCount) }}</strong>
            <span class="kpi-meta">Pending signature, signed, or active.</span>
        </article>
        <article class="panel kpi-card kpi-card--pipeline">
            <span class="metric-label">Signed agreements</span>
            <strong class="kpi-value">{{ number_format($signedCount) }}</strong>
            <span class="kpi-meta">Ready to use as the engagement record.</span>
        </article>
        <article class="panel kpi-card kpi-card--traffic">
            <span class="metric-label">Invoices available</span>
            <strong class="kpi-value">{{ number_format($invoiceCount) }}</strong>
            <span class="kpi-meta">The required source for every new contract.</span>
        </article>
    </section>

    <section class="panel panel-padded" style="margin-top: 24px;">
        <div class="panel-head">
            <span class="eyebrow">Contract register</span>
            <h2>Invoice-linked staff documents</h2>
            <p>Preview, edit, or export the latest version of an agreement.</p>
        </div>

        <div class="table-wrap">
            <table class="quote-table">
                <thead>
                    <tr>
                        <th>Contract</th>
                        <th>Staff member</th>
                        <th>Project</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        <tr>
                            <td>
                                <strong>{{ $contract->contract_number }}</strong>
                                <span>Updated {{ optional($contract->updated_at)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <strong>{{ $contract->staff_name }}</strong>
                                <span>{{ $contract->staff_role }}</span>
                            </td>
                            <td>
                                <strong>{{ $contract->project->name }}</strong>
                                <span>{{ $contract->project->project_number }}{{ $contract->project->client_company ? ' / '.$contract->project->client_company : '' }}</span>
                                @if ($contract->invoice)
                                    <span>Invoice {{ $contract->invoice->quote_number }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $contract->currency }} {{ number_format((float) $contract->agreed_fee, 2) }}</strong>
                                <span>Agreed fee</span>
                            </td>
                            <td>
                                <span class="contract-status contract-status--{{ $contract->status }}">{{ str_replace('_', ' ', $contract->status) }}</span>
                            </td>
                            <td>
                                <details class="action-menu">
                                    <summary>Actions</summary>
                                    <div class="action-menu-panel">
                                        <a href="{{ route('admin.staff-contracts.show', $contract) }}">Preview</a>
                                        @if ($contract->hasSignedDocument())
                                            <span>Locked after signed upload</span>
                                        @else
                                            <a href="{{ route('admin.staff-contracts.edit', $contract) }}">Edit</a>
                                        @endif
                                        <a href="{{ route('admin.staff-contracts.pdf', $contract) }}">Download PDF</a>
                                        @if ($contract->hasSignedDocument())
                                            <a href="{{ route('admin.staff-contracts.signed-document', $contract) }}">Download signed copy</a>
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <strong>No staff contracts created yet.</strong>
                                <span>Create an invoice first, then attach the agreement to that existing invoice.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
