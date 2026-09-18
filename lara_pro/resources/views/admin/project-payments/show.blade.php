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
        <div class="ppi-actions"><a class="button" href="{{ route('admin.project-payments.pdf', $invoice) }}">Download PDF</a><a class="ghost-button" href="{{ route('admin.project-payments.edit', $invoice) }}">Edit invoice</a><a class="ghost-button" href="{{ route('admin.project-payments.receipts.index', ['project' => $invoice->project_id]) }}">Payment receipts</a><a class="ghost-button" href="{{ route('admin.project-payments.index') }}">All payment invoices</a></div>
    </section>

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

    <section class="panel ppi-card">
        <div class="ppi-card-head"><div><span class="eyebrow">Proof of payment</span><h3>Receipts linked to this invoice</h3><p>Keep part-payment, final-payment, staff, or other payment evidence with the project record.</p></div><span class="ppi-secure-badge">Private persistent storage</span></div>
        <div class="ppi-receipt-grid">
            <div>
                @if ($canManagePayments)
                    <form class="ppi-payment-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.project-payments.receipts.store') }}">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $invoice->project_id }}">
                        <input type="hidden" name="project_payment_invoice_id" value="{{ $invoice->id }}">
                        <div class="ppi-grid-2">
                            <div class="field"><label for="receipt_payment_type">Payment type</label><select id="receipt_payment_type" name="payment_type" required>@foreach ($paymentTypes as $value => $label)<option value="{{ $value }}" @selected(old('payment_type', 'project_part_payment') === $value)>{{ $label }}</option>@endforeach</select></div>
                            <div class="field"><label for="receipt_paid_on">Payment date</label><input id="receipt_paid_on" name="paid_on" type="date" value="{{ old('paid_on', now()->toDateString()) }}" required></div>
                            <div class="field"><label for="receipt_amount">Amount paid</label><input id="receipt_amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount', number_format((float) $invoice->total_amount, 2, '.', '')) }}" required></div>
                            <div class="field"><label for="receipt_currency">Currency</label><input id="receipt_currency" name="currency" value="{{ old('currency', $invoice->currency) }}" maxlength="3" required></div>
                            <div class="field ppi-field-wide" data-staff-contract-field><label for="receipt_staff_contract">Staff contract <span style="color:var(--muted);font-weight:400">(for staff payments)</span></label><select id="receipt_staff_contract" name="staff_contract_id"><option value="">Select staff contract</option>@foreach ($invoice->project?->staffContracts ?? [] as $contract)<option value="{{ $contract->id }}" @selected((string) old('staff_contract_id') === (string) $contract->id)>{{ $contract->contract_number }} · {{ $contract->staff_name }} · {{ $contract->staff_role }}</option>@endforeach</select></div>
                            <div class="field"><label for="receipt_counterparty">Paid to / received from</label><input id="receipt_counterparty" name="counterparty" value="{{ old('counterparty', $invoice->client_company) }}" placeholder="Person or company"></div>
                            <div class="field"><label for="receipt_reference">Payment reference</label><input id="receipt_reference" name="reference" value="{{ old('reference') }}" placeholder="Transfer or transaction ID"></div>
                            <div class="field ppi-field-wide"><label for="receipt_notes">Internal note</label><textarea id="receipt_notes" name="notes" rows="3" placeholder="Optional payment context">{{ old('notes') }}</textarea></div>
                            <div class="field ppi-field-wide"><label for="receipt_file">Receipt file</label><input class="ppi-file-input" id="receipt_file" name="receipt" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" required><small>PDF, JPG, PNG or WebP, up to 20 MB.</small></div>
                        </div>
                        <div class="ppi-upload-note">The file is stored privately outside the application release, so a Git push or deployment does not replace it. Only its path and metadata are kept in the database.</div>
                        <button class="button" type="submit">Upload receipt</button>
                    </form>
                @else
                    <div class="ppi-upload-note">Only a full administrator can add payment receipts. You can still review and download existing evidence.</div>
                @endif
            </div>
            <div>
                @if ($invoice->receipts->isEmpty())
                    <div class="ppi-empty" style="padding:32px 20px"><h3>No receipt uploaded yet.</h3><p>The first payment receipt linked to this invoice will appear here.</p></div>
                @else
                    <div class="ppi-receipt-list">
                        @foreach ($invoice->receipts as $receipt)
                            <article class="ppi-receipt-item">
                                <div><h4>{{ $receipt->typeLabel() }} · {{ $receipt->currency }} {{ number_format((float) $receipt->amount, 2) }}</h4><p>{{ $receipt->paid_on->format('M d, Y') }}@if ($receipt->staffContract) · {{ $receipt->staffContract->staff_name }}@endif @if ($receipt->counterparty) · {{ $receipt->counterparty }}@endif<br>{{ $receipt->receipt_original_name }} · {{ $receipt->sizeLabel() }}@if ($receipt->reference) · Ref: {{ $receipt->reference }}@endif</p></div>
                                <div class="ppi-actions"><a class="ghost-button" target="_blank" rel="noopener" href="{{ route('admin.project-payments.receipts.preview', $receipt) }}">Preview</a><a class="ghost-button" href="{{ route('admin.project-payments.receipts.download', $receipt) }}">Download</a></div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
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
