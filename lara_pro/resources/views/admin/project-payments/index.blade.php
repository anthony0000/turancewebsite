@extends('admin.layouts.app')

@section('title', 'Project Payment Invoices')

@push('styles')
    @include('admin.project-payments.partials.styles')
@endpush

@section('content')
@php
    $invoiceCount = (int) ($summary->invoice_count ?? 0);
    $paidCount = (int) ($summary->paid_count ?? 0);
    $openCount = max(0, $invoiceCount - $paidCount);
    $collectionRate = $invoiceCount > 0 ? (int) round(($paidCount / $invoiceCount) * 100) : 0;
@endphp
<div class="ppi-page ppi-index-page">
    <section class="panel ppi-index-hero">
        <div class="ppi-index-hero__copy">
            <span class="ppi-kicker"><span></span>Progress billing</span>
            <h2>Turn project progress<br>into <em>paid work.</em></h2>
            <p>Create accurate milestone invoices, track collections, and keep every payment receipt connected to the project.</p>
            <div class="ppi-actions ppi-index-hero__actions">
                <a class="button ppi-primary-action" href="{{ route('admin.project-payments.create') }}">
                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 4v12M4 10h12"/></svg>
                    New payment invoice
                </a>
                <a class="ppi-text-action" href="{{ route('admin.project-payments.receipts.index') }}">
                    View payment receipts
                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m7 4 6 6-6 6"/></svg>
                </a>
            </div>
        </div>
        <div class="ppi-collection-card">
            <div class="ppi-collection-card__top"><span>Collection rate</span><span class="ppi-live-dot">Live</span></div>
            <div class="ppi-collection-card__body">
                <div class="ppi-rate-ring" style="--ppi-rate: {{ $collectionRate }}"><strong>{{ $collectionRate }}%</strong><span>settled</span></div>
                <div class="ppi-collection-summary">
                    <strong>{{ $paidCount }} of {{ $invoiceCount }}</strong>
                    <span>invoices collected</span>
                    <div class="ppi-collection-summary__line"><span>Open invoices</span><b>{{ $openCount }}</b></div>
                </div>
            </div>
        </div>
    </section>

    <section class="ppi-kpis" aria-label="Payment invoice summary">
        <article class="panel ppi-kpi ppi-kpi--neutral"><div class="ppi-kpi__head"><span>All invoices</span><span class="ppi-kpi__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h8l4 4v14H7zM15 3v5h4M10 12h6M10 16h6"/></svg></span></div><strong>{{ number_format($invoiceCount) }}</strong><small>Payment requests created</small></article>
        <article class="panel ppi-kpi ppi-kpi--paid"><div class="ppi-kpi__head"><span>Paid invoices</span><span class="ppi-kpi__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 12 3 3 7-7M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/></svg></span></div><strong>{{ number_format($paidCount) }}</strong><small>{{ $openCount }} still awaiting payment</small></article>
        <article class="panel ppi-kpi ppi-kpi--open"><div class="ppi-kpi__head"><span>Outstanding</span><span class="ppi-kpi__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 7v5l3 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/></svg></span></div><strong>{{ $outstandingTotal }}</strong><small>Across open invoices</small></article>
        <article class="panel ppi-kpi ppi-kpi--collected"><div class="ppi-kpi__head"><span>Total collected</span><span class="ppi-kpi__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v12H4zM7 7V5h10v2M8 13h8M12 10v6"/></svg></span></div><strong>{{ $paidTotal }}</strong><small>Confirmed project revenue</small></article>
    </section>

    <form class="panel ppi-toolbar" method="GET" action="{{ route('admin.project-payments.index') }}">
        <div class="ppi-toolbar__head"><div><span class="eyebrow">Invoice finder</span><h3>Find a payment request</h3></div><span class="ppi-result-count">{{ $invoices->total() }} {{ Str::plural('result', $invoices->total()) }}</span></div>
        <div class="ppi-filter-grid">
            <div class="field ppi-search-field"><label for="payment-search">Search</label><div class="ppi-input-shell"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/></svg><input id="payment-search" name="q" value="{{ $filters['q'] }}" placeholder="Invoice number, project, or client"></div></div>
            <div class="field"><label for="payment-status">Status</label><select id="payment-status" name="status"><option value="">All statuses</option><option value="unpaid" @selected($filters['status'] === 'unpaid')>Unpaid</option><option value="paid" @selected($filters['status'] === 'paid')>Paid</option></select></div>
            <div class="field"><label for="payment-project">Project</label><select id="payment-project" name="project"><option value="">All projects</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected($filters['project'] === $project->id)>{{ $project->project_number }} · {{ $project->name }}</option>@endforeach</select></div>
            <div class="ppi-actions ppi-filter-actions"><button class="button" type="submit">Apply filters</button>@if ($filters['q'] || $filters['status'] || $filters['project'])<a class="ghost-button" href="{{ route('admin.project-payments.index') }}">Clear</a>@endif</div>
        </div>
    </form>

    <section class="panel ppi-invoice-panel">
        <div class="ppi-table-heading"><div><span class="eyebrow">Payment register</span><h3>Recent invoices</h3></div><a href="{{ route('admin.project-payments.receipts.index') }}">Open receipt ledger <span>→</span></a></div>
        @if ($invoices->isEmpty())
            <div class="ppi-empty"><h3>No payment invoices yet.</h3><p>Your first project progress invoice will appear here with its amount and payment status.</p><a class="button" href="{{ route('admin.project-payments.create') }}">Generate first invoice</a></div>
        @else
            <div class="ppi-table-wrap">
                <table class="ppi-table">
                    <thead><tr><th>Invoice</th><th>Project / client</th><th>Progress</th><th>Amount</th><th>Issued</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td><div class="ppi-invoice-identity"><span class="ppi-invoice-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h8l4 4v14H7zM15 3v5h4M10 12h6M10 16h4"/></svg></span><div><a href="{{ route('admin.project-payments.show', $invoice) }}">{{ $invoice->invoice_number }}</a><small>{{ $invoice->original_invoice_number ? 'Linked to '.$invoice->original_invoice_number : 'Standalone progress invoice' }}</small></div></div></td>
                            <td><strong>{{ $invoice->project_title }}</strong><small>{{ $invoice->client_company }} · {{ $invoice->project?->project_number }}</small></td>
                            <td><div class="ppi-progress-cell"><div><strong>{{ number_format((float) $invoice->completion_percentage, 0) }}%</strong><span>complete</span></div><div class="ppi-progress-track"><span style="width:{{ min(100, max(0, (float) $invoice->completion_percentage)) }}%"></span></div></div></td>
                            <td><strong class="ppi-amount-primary">{{ $invoice->currencySymbol() }}{{ number_format((float) $invoice->total_amount, 2) }}</strong><small>{{ $invoice->local_currency }} {{ number_format($invoice->local_total_amount, 0) }}</small></td>
                            <td><strong class="ppi-date-primary">{{ $invoice->issue_date->format('M d, Y') }}</strong><small>{{ $invoice->due_date ? 'Due '.$invoice->due_date->format('M d') : $invoice->payment_terms }}</small></td>
                            <td><span class="ppi-status {{ $invoice->isPaid() ? 'ppi-status--paid' : '' }}">{{ $invoice->isPaid() ? 'Paid' : 'Unpaid' }}</span></td>
                            <td><a class="ppi-row-action" href="{{ route('admin.project-payments.show', $invoice) }}" aria-label="Open {{ $invoice->invoice_number }}"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m7 4 6 6-6 6"/></svg></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
    <div>{{ $invoices->links() }}</div>
</div>
@endsection
