@extends('admin.layouts.app')

@section('title', 'Project Payment Receipts')

@push('styles')
    @include('admin.project-payments.partials.styles')
@endpush

@section('content')
<div class="ppi-page">
    <section class="panel ppi-hero">
        <div><span class="eyebrow">Payment evidence</span><h2>One receipt ledger for every project payment.</h2><p>Track project and staff part payments, final payments, and any other transaction with private supporting evidence.</p></div>
        <div class="ppi-actions">@if ($canManagePayments)<a class="button" href="{{ route('admin.project-payments.receipts.create') }}">Upload receipt</a>@endif<a class="ghost-button" href="{{ route('admin.project-payments.index') }}">Payment invoices</a></div>
    </section>

    <section class="ppi-kpis" aria-label="Receipt summary">
        <article class="panel ppi-kpi"><span>Receipts</span><strong>{{ number_format($receiptCount) }}</strong></article>
        <article class="panel ppi-kpi" style="grid-column:span 2"><span>Recorded payments</span><strong>{{ $recordedTotal }}</strong></article>
        <article class="panel ppi-kpi"><span>Storage</span><strong style="font-size:20px">Private & persistent</strong></article>
    </section>

    <form class="panel ppi-toolbar" method="GET" action="{{ route('admin.project-payments.receipts.index') }}">
        <div class="ppi-filter-grid">
            <div class="field"><label for="receipt-search">Search</label><input id="receipt-search" name="q" value="{{ $filters['q'] }}" placeholder="Project, staff, reference, or file"></div>
            <div class="field"><label for="receipt-type-filter">Payment type</label><select id="receipt-type-filter" name="type"><option value="">All payment types</option>@foreach ($paymentTypes as $value => $label)<option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="field"><label for="receipt-project-filter">Project</label><select id="receipt-project-filter" name="project"><option value="">All projects</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected($filters['project'] === $project->id)>{{ $project->project_number }} · {{ $project->name }}</option>@endforeach</select></div>
            <div class="ppi-actions"><button class="button" type="submit">Filter</button><a class="ghost-button" href="{{ route('admin.project-payments.receipts.index') }}">Reset</a></div>
        </div>
    </form>

    <section class="panel">
        @if ($receipts->isEmpty())
            <div class="ppi-empty"><h3>No payment receipts yet.</h3><p>Your first project or staff payment receipt will appear here.</p>@if ($canManagePayments)<a class="button" href="{{ route('admin.project-payments.receipts.create') }}">Upload first receipt</a>@endif</div>
        @else
            <div class="ppi-table-wrap"><table class="ppi-table">
                <thead><tr><th>Payment</th><th>Project / link</th><th>Counterparty</th><th>Amount</th><th>Receipt</th><th></th></tr></thead>
                <tbody>@foreach ($receipts as $receipt)<tr>
                    <td><strong>{{ $receipt->typeLabel() }}</strong><small>{{ $receipt->paid_on->format('M d, Y') }}</small></td>
                    <td><strong>{{ $receipt->project?->name }}</strong><small>{{ $receipt->project?->project_number }}@if ($receipt->invoice) · {{ $receipt->invoice->invoice_number }}@elseif ($receipt->staffContract) · {{ $receipt->staffContract->contract_number }}@endif</small></td>
                    <td>{{ $receipt->counterparty ?: ($receipt->staffContract?->staff_name ?: 'Not specified') }}<small>{{ $receipt->reference ? 'Ref: '.$receipt->reference : 'No reference' }}</small></td>
                    <td><strong>{{ $receipt->currency }} {{ number_format((float) $receipt->amount, 2) }}</strong></td>
                    <td>{{ $receipt->receipt_original_name }}<small>{{ $receipt->sizeLabel() }}</small></td>
                    <td><div class="ppi-actions"><a class="ghost-button" target="_blank" rel="noopener" href="{{ route('admin.project-payments.receipts.preview', $receipt) }}">Preview</a><a class="ghost-button" href="{{ route('admin.project-payments.receipts.download', $receipt) }}">Download</a></div></td>
                </tr>@endforeach</tbody>
            </table></div>
        @endif
    </section>
    <div>{{ $receipts->links() }}</div>
</div>
@endsection
