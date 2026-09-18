@extends('admin.layouts.app')

@section('title', 'Project Payment Invoices')

@push('styles')
    @include('admin.project-payments.partials.styles')
@endpush

@section('content')
<div class="ppi-page">
    <section class="panel ppi-hero">
        <div><span class="eyebrow">Progress billing</span><h2>Project payments, tied directly to delivery progress.</h2><p>Generate an editable payment request from a pipeline project, export the client PDF, and keep its payment status visible.</p></div>
        <div class="ppi-actions"><a class="button" href="{{ route('admin.project-payments.create') }}">New payment invoice</a></div>
    </section>

    <section class="ppi-kpis" aria-label="Payment invoice summary">
        <article class="panel ppi-kpi"><span>Invoices</span><strong>{{ number_format((int) ($summary->invoice_count ?? 0)) }}</strong></article>
        <article class="panel ppi-kpi"><span>Paid</span><strong>{{ number_format((int) ($summary->paid_count ?? 0)) }}</strong></article>
        <article class="panel ppi-kpi"><span>Outstanding</span><strong>{{ $outstandingTotal }}</strong></article>
        <article class="panel ppi-kpi"><span>Collected</span><strong>{{ $paidTotal }}</strong></article>
    </section>

    <form class="panel ppi-toolbar" method="GET" action="{{ route('admin.project-payments.index') }}">
        <div class="ppi-filter-grid">
            <div class="field"><label for="payment-search">Search</label><input id="payment-search" name="q" value="{{ $filters['q'] }}" placeholder="Invoice, project, or client"></div>
            <div class="field"><label for="payment-status">Status</label><select id="payment-status" name="status"><option value="">All statuses</option><option value="unpaid" @selected($filters['status'] === 'unpaid')>Unpaid</option><option value="paid" @selected($filters['status'] === 'paid')>Paid</option></select></div>
            <div class="field"><label for="payment-project">Project</label><select id="payment-project" name="project"><option value="">All projects</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected($filters['project'] === $project->id)>{{ $project->project_number }} · {{ $project->name }}</option>@endforeach</select></div>
            <div class="ppi-actions"><button class="button" type="submit">Filter</button><a class="ghost-button" href="{{ route('admin.project-payments.index') }}">Reset</a></div>
        </div>
    </form>

    <section class="panel">
        @if ($invoices->isEmpty())
            <div class="ppi-empty"><h3>No payment invoices yet.</h3><p>Your first project progress invoice will appear here with its amount and payment status.</p><a class="button" href="{{ route('admin.project-payments.create') }}">Generate first invoice</a></div>
        @else
            <div class="ppi-table-wrap">
                <table class="ppi-table">
                    <thead><tr><th>Invoice</th><th>Project / client</th><th>Progress</th><th>Amount</th><th>Issued</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td><a href="{{ route('admin.project-payments.show', $invoice) }}">{{ $invoice->invoice_number }}</a><small>Original: {{ $invoice->original_invoice_number ?: 'Not linked' }}</small></td>
                            <td><strong>{{ $invoice->project_title }}</strong><small>{{ $invoice->client_company }} · {{ $invoice->project?->project_number }}</small></td>
                            <td><strong>{{ number_format((float) $invoice->completion_percentage, 2) }}%</strong></td>
                            <td><strong>{{ $invoice->currencySymbol() }}{{ number_format((float) $invoice->total_amount, 2) }}</strong><small>{{ $invoice->local_currency }} {{ number_format($invoice->local_total_amount, 0) }}</small></td>
                            <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                            <td><span class="ppi-status {{ $invoice->isPaid() ? 'ppi-status--paid' : '' }}">{{ $invoice->isPaid() ? 'Paid' : 'Unpaid' }}</span></td>
                            <td><a class="ghost-button" href="{{ route('admin.project-payments.show', $invoice) }}">Open</a></td>
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
