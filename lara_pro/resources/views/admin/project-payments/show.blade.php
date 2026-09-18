@extends('admin.layouts.app')

@section('title', $invoice->invoice_number.' | Payment Invoice')

@push('styles')
    @include('admin.project-payments.partials.styles')
    <style>@include('admin.project-payments.partials.document-styles')</style>
@endpush

@section('content')
<div class="ppi-page">
    <section class="panel ppi-hero">
        <div><span class="eyebrow">{{ $invoice->isPaid() ? 'Payment received' : 'Payment request' }}</span><h2>{{ $invoice->invoice_number }} is ready for review.</h2><p>{{ $invoice->project_title }} · {{ number_format((float) $invoice->completion_percentage, 2) }}% progress payment · {{ $invoice->client_company }}</p></div>
        <div class="ppi-actions"><a class="button" href="{{ route('admin.project-payments.pdf', $invoice) }}">Download PDF</a><a class="ghost-button" href="{{ route('admin.project-payments.edit', $invoice) }}">Edit invoice</a><a class="ghost-button" href="{{ route('admin.project-payments.index') }}">All payment invoices</a></div>
    </section>

    <div class="ppi-detail-grid">
        <section class="panel ppi-document-stage"><div class="ppi-document-frame">@include('admin.project-payments.partials.document')</div></section>
        <aside class="ppi-stack">
            <section class="panel ppi-card">
                <div class="ppi-card-head"><div><span class="eyebrow">Status</span><h3>Payment tracking</h3></div><span class="ppi-status {{ $invoice->isPaid() ? 'ppi-status--paid' : '' }}">{{ $invoice->isPaid() ? 'Paid' : 'Unpaid' }}</span></div>
                @if ($invoice->isPaid())
                    <div class="ppi-paid-note"><strong>Paid {{ $invoice->paid_at->format('M d, Y') }}</strong><br>{{ $invoice->paidBy?->name ? 'Marked by '.$invoice->paidBy->name.'.' : 'Payment recorded by an administrator.' }}@if ($invoice->payment_reference)<br>Reference: {{ $invoice->payment_reference }}@endif</div>
                    @if ($invoice->payment_notes)<p style="color:var(--muted);line-height:1.6">{{ $invoice->payment_notes }}</p>@endif
                @elseif ($canMarkPaid)
                    <form class="ppi-payment-form" method="POST" action="{{ route('admin.project-payments.paid', $invoice) }}" onsubmit="return confirm('Mark {{ $invoice->invoice_number }} as paid?');">
                        @csrf @method('PATCH')
                        <div class="field"><label for="paid_on">Payment date</label><input id="paid_on" name="paid_on" type="date" value="{{ now()->toDateString() }}"></div>
                        <div class="field"><label for="payment_reference">Payment reference</label><input id="payment_reference" name="payment_reference" placeholder="Transfer or receipt reference"></div>
                        <div class="field"><label for="payment_notes">Internal note</label><textarea id="payment_notes" name="payment_notes" rows="3" placeholder="Optional reconciliation note"></textarea></div>
                        <button class="button" type="submit">Mark as paid</button>
                    </form>
                @else
                    <p style="margin:0;color:var(--muted)">Only a full administrator can mark this invoice as paid.</p>
                @endif
            </section>

            <section class="panel ppi-card">
                <div class="ppi-card-head"><div><span class="eyebrow">Invoice snapshot</span><h3>Commercial details</h3></div></div>
                <div class="ppi-meta">
                    <div class="ppi-meta-row"><span>Project</span><strong>{{ $invoice->project?->project_number }} · {{ $invoice->project_title }}</strong></div>
                    <div class="ppi-meta-row"><span>Contract value</span><strong>{{ $invoice->currencySymbol() }}{{ number_format((float) $invoice->contract_value, 2) }}</strong></div>
                    <div class="ppi-meta-row"><span>Payment percentage</span><strong>{{ number_format((float) $invoice->completion_percentage, 2) }}%</strong></div>
                    <div class="ppi-meta-row"><span>Total due</span><strong>{{ $invoice->currencySymbol() }}{{ number_format((float) $invoice->total_amount, 2) }}</strong></div>
                    <div class="ppi-meta-row"><span>Original invoice</span><strong>{{ $invoice->original_invoice_number ?: 'Not linked' }}</strong></div>
                    <div class="ppi-meta-row"><span>Last updated</span><strong>{{ $invoice->updated_at->format('M d, Y H:i') }}</strong></div>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
