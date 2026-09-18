@extends('admin.layouts.app')

@section('title', $invoice->invoice_number.' | Payment Invoice')

@push('styles')
    @include('admin.project-payments.partials.styles')
    <style>@include('admin.project-payments.partials.document-styles')</style>
@endpush

@section('content')
<div class="ppi-page">
    <header class="ppi-invoice-topbar">
        <div class="ppi-invoice-heading">
            <div class="ppi-invoice-heading__eyebrow"><span class="eyebrow">Payment invoice</span><span class="ppi-status {{ $invoice->isPaid() ? 'ppi-status--paid' : '' }}">{{ $invoice->isPaid() ? 'Paid' : 'Unpaid' }}</span></div>
            <h2>{{ $invoice->invoice_number }}</h2>
            <div class="ppi-invoice-heading__meta"><span>{{ $invoice->project_title }}</span><i></i><span>{{ number_format((float) $invoice->completion_percentage, 2) }}% milestone</span><i></i><span>{{ $invoice->client_company }}</span></div>
        </div>
        <div class="ppi-actions ppi-invoice-topbar__actions">
            <a class="button" href="{{ route('admin.project-payments.pdf', $invoice) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>Download PDF</a>
            <a class="ghost-button" href="{{ route('admin.project-payments.edit', $invoice) }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 20 4.5-1 10-10-3.5-3.5-10 10L4 20ZM13.5 6.5 17 10"/></svg>Edit</a>
            <a class="ghost-button" href="#payment-receipts"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6M9 12h6"/></svg>Receipts</a>
            <a class="ppi-back-link" href="{{ route('admin.project-payments.index') }}">Back to payments</a>
        </div>
    </header>

    @if ($errors->any())<div class="ppi-errors"><strong>The receipt could not be uploaded.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

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

    <section id="payment-receipts" class="panel ppi-receipt-workspace">
        <div class="ppi-receipt-workspace__head">
            <div><span class="eyebrow">Payment evidence</span><h3>Receipts</h3><p>Files attached to payments recorded against this invoice.</p></div>
            <span class="ppi-receipt-count">{{ $invoice->receipts->count() }} {{ Str::plural('receipt', $invoice->receipts->count()) }}</span>
        </div>

        @if ($invoice->receipts->isEmpty())
            <div class="ppi-receipt-empty"><span class="ppi-proof-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6M9 12h6"/></svg></span><div><h4>No receipts attached</h4><p>Add the first payment receipt when funds are received or disbursed.</p></div></div>
        @else
            <div class="ppi-proof-list">
                @foreach ($invoice->receipts as $receipt)
                    <article class="ppi-proof-row">
                        <span class="ppi-proof-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6M9 12h6"/></svg></span>
                        <div class="ppi-proof-row__main"><h4>{{ $receipt->typeLabel() }}</h4><p>{{ $receipt->paid_on->format('M d, Y') }}@if ($receipt->counterparty) · {{ $receipt->counterparty }}@endif @if ($receipt->staffContract) · {{ $receipt->staffContract->staff_name }}@endif</p><small>{{ $receipt->receipt_original_name }} · {{ $receipt->sizeLabel() }}@if ($receipt->reference) · Ref {{ $receipt->reference }}@endif</small></div>
                        <strong class="ppi-proof-row__amount">{{ $receipt->currency }} {{ number_format((float) $receipt->amount, 2) }}</strong>
                        <div class="ppi-actions ppi-proof-row__actions"><a class="ghost-button" target="_blank" rel="noopener" href="{{ route('admin.project-payments.receipts.preview', $receipt) }}">Preview</a><a class="ghost-button" href="{{ route('admin.project-payments.receipts.download', $receipt) }}">Download</a></div>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($canManagePayments)
            <details class="ppi-receipt-compose" @if ($errors->any()) open @endif>
                <summary><span><b>+</b> Add payment receipt</span><small>Project, staff, part or full payment</small><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 7 5 5 5-5"/></svg></summary>
                <div class="ppi-receipt-compose__body">
                    <form class="ppi-payment-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.project-payments.receipts.store') }}">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $invoice->project_id }}">
                        <input type="hidden" name="project_payment_invoice_id" value="{{ $invoice->id }}">
                        <div class="ppi-receipt-form-grid">
                            <div class="field"><label for="receipt_payment_type">Payment type</label><select id="receipt_payment_type" name="payment_type" required>@foreach ($paymentTypes as $value => $label)<option value="{{ $value }}" @selected(old('payment_type', 'project_part_payment') === $value)>{{ $label }}</option>@endforeach</select></div>
                            <div class="field"><label for="receipt_paid_on">Payment date</label><input id="receipt_paid_on" name="paid_on" type="date" value="{{ old('paid_on', now()->toDateString()) }}" required></div>
                            <div class="field"><label for="receipt_amount">Amount paid</label><input id="receipt_amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount', number_format((float) $invoice->total_amount, 2, '.', '')) }}" required></div>
                            <div class="field"><label for="receipt_currency">Currency</label><input id="receipt_currency" name="currency" value="{{ old('currency', $invoice->currency) }}" maxlength="3" required></div>
                            <div class="field ppi-receipt-form-span-2" data-staff-contract-field><label for="receipt_staff_contract">Staff contract <span>(staff payments only)</span></label><select id="receipt_staff_contract" name="staff_contract_id"><option value="">Select staff contract</option>@foreach ($invoice->project?->staffContracts ?? [] as $contract)<option value="{{ $contract->id }}" @selected((string) old('staff_contract_id') === (string) $contract->id)>{{ $contract->contract_number }} · {{ $contract->staff_name }} · {{ $contract->staff_role }}</option>@endforeach</select></div>
                            <div class="field"><label for="receipt_counterparty">Paid to / received from</label><input id="receipt_counterparty" name="counterparty" value="{{ old('counterparty', $invoice->client_company) }}" placeholder="Person or company"></div>
                            <div class="field"><label for="receipt_reference">Payment reference</label><input id="receipt_reference" name="reference" value="{{ old('reference') }}" placeholder="Transfer or transaction ID"></div>
                            <div class="field ppi-receipt-form-span-2"><label for="receipt_notes">Internal note</label><textarea id="receipt_notes" name="notes" rows="3" placeholder="Optional payment context">{{ old('notes') }}</textarea></div>
                            <div class="field ppi-receipt-form-span-2"><label for="receipt_file">Receipt file</label><input class="ppi-file-input" id="receipt_file" name="receipt" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" required><small>PDF, JPG, PNG or WebP · maximum 20 MB</small></div>
                        </div>
                        <div class="ppi-receipt-submit"><button class="button" type="submit">Upload receipt</button></div>
                    </form>
                </div>
            </details>
        @endif
    </section>
</div>
@if ($canManagePayments)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const type = document.getElementById('receipt_payment_type');
    const contract = document.getElementById('receipt_staff_contract');
    const syncStaffRequirement = () => {
        const isStaffPayment = type && type.value.startsWith('staff_');
        if (contract) contract.required = isStaffPayment;
    };
    type?.addEventListener('change', syncStaffRequirement);
    syncStaffRequirement();
});
</script>
@endif
@endsection
