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
<div class="ppi-page ppi-dashboard">
    <header class="ppi-dashboard-header">
        <div>
            <span class="eyebrow">Project finance</span>
            <h2>Project payments</h2>
            <p>Create milestone invoices, follow outstanding balances, and keep receipts in one register.</p>
        </div>
        <div class="ppi-actions ppi-dashboard-header__actions">
            <a class="ghost-button" href="{{ route('admin.project-payments.receipts.index') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6M9 12h6"/></svg>
                Receipts
            </a>
            <a class="button" href="{{ route('admin.project-payments.create') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New payment invoice
            </a>
        </div>
    </header>

    <section class="ppi-summary-grid" aria-label="Payment invoice summary">
        <article class="panel ppi-summary-card">
            <div class="ppi-summary-card__top"><span>Invoices</span><span class="ppi-summary-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h8l4 4v14H7zM15 3v5h4M10 12h6M10 16h6"/></svg></span></div>
            <strong>{{ number_format($invoiceCount) }}</strong>
            <div class="ppi-summary-card__foot"><span>Total requests</span><b>{{ $paidCount }} settled</b></div>
        </article>
        <article class="panel ppi-summary-card">
            <div class="ppi-summary-card__top"><span>Open invoices</span><span class="ppi-summary-icon ppi-summary-icon--amber"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 7v5l3 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/></svg></span></div>
            <strong>{{ number_format($openCount) }}</strong>
            <div class="ppi-summary-card__foot"><span>Awaiting payment</span><b>{{ 100 - $collectionRate }}%</b></div>
        </article>
        <article class="panel ppi-summary-card">
            <div class="ppi-summary-card__top"><span>Outstanding</span><span class="ppi-summary-icon ppi-summary-icon--amber"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 6v12M16 9.5c0-1.4-1.8-2.5-4-2.5S8 8.1 8 9.5s1.8 2.5 4 2.5 4 1.1 4 2.5-1.8 2.5-4 2.5-4-1.1-4-2.5"/></svg></span></div>
            <strong>{{ $outstandingTotal }}</strong>
            <div class="ppi-summary-card__foot"><span>Receivable</span><b>{{ $openCount }} {{ Str::plural('invoice', $openCount) }}</b></div>
        </article>
        <article class="panel ppi-summary-card ppi-summary-card--collected">
            <div class="ppi-summary-card__top"><span>Collected</span><span class="ppi-summary-icon ppi-summary-icon--green"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m7 12 3 3 7-7M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/></svg></span></div>
            <strong>{{ $paidTotal }}</strong>
            <div class="ppi-summary-card__foot"><span>Collection performance</span><b>{{ $collectionRate }}%</b></div>
            <div class="ppi-summary-progress"><span style="width:{{ $collectionRate }}%"></span></div>
        </article>
    </section>

    <section class="panel ppi-register">
        <div class="ppi-register__head">
            <div><span class="eyebrow">Payment register</span><h3>Invoices</h3></div>
            <span class="ppi-register__count">{{ $invoices->total() }} {{ Str::plural('record', $invoices->total()) }}</span>
        </div>

        <form class="ppi-filter-bar" method="GET" action="{{ route('admin.project-payments.index') }}">
            <div class="ppi-filter-search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/></svg><label class="sr-only" for="payment-search">Search invoices</label><input id="payment-search" name="q" value="{{ $filters['q'] }}" placeholder="Search invoice, project, or client"></div>
            <div class="ppi-filter-select"><label class="sr-only" for="payment-status">Status</label><select id="payment-status" name="status"><option value="">All statuses</option><option value="unpaid" @selected($filters['status'] === 'unpaid')>Unpaid</option><option value="paid" @selected($filters['status'] === 'paid')>Paid</option></select></div>
            <div class="ppi-filter-select ppi-filter-select--project"><label class="sr-only" for="payment-project">Project</label><select id="payment-project" name="project"><option value="">All projects</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected($filters['project'] === $project->id)>{{ $project->project_number }} · {{ $project->name }}</option>@endforeach</select></div>
            <button class="button" type="submit">Filter</button>
            @if ($filters['q'] || $filters['status'] || $filters['project'])<a class="ppi-clear-filter" href="{{ route('admin.project-payments.index') }}">Clear</a>@endif
        </form>

        @if ($invoices->isEmpty())
            <div class="ppi-empty"><h3>No payment invoices yet.</h3><p>Your first project progress invoice will appear here with its amount and payment status.</p><a class="button" href="{{ route('admin.project-payments.create') }}">Generate first invoice</a></div>
        @else
            <div class="ppi-table-wrap">
                <table class="ppi-table ppi-register-table">
                    <thead><tr><th>Invoice</th><th>Project / client</th><th>Progress</th><th>Amount</th><th>Issued</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td><div class="ppi-invoice-identity"><span class="ppi-invoice-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h8l4 4v14H7zM15 3v5h4M10 12h6M10 16h4"/></svg></span><div><a href="{{ route('admin.project-payments.show', $invoice) }}">{{ $invoice->invoice_number }}</a><small>{{ $invoice->original_invoice_number ? 'Linked to '.$invoice->original_invoice_number : 'Progress invoice' }}</small></div></div></td>
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
