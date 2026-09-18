@extends('admin.layouts.app')

@section('title', 'Upload Payment Receipt')

@push('styles')
    @include('admin.project-payments.partials.styles')
@endpush

@section('content')
<div class="ppi-page">
    <section class="panel ppi-hero">
        <div><span class="eyebrow">Record payment</span><h2>Upload proof for any project payment.</h2><p>Use this for project installments or final payments, staff payments, and other project-related transactions.</p></div>
        <div class="ppi-actions"><a class="ghost-button" href="{{ route('admin.project-payments.receipts.index') }}">Receipt ledger</a><a class="ghost-button" href="{{ route('admin.project-payments.index') }}">Payment invoices</a></div>
    </section>

    @if ($errors->any())<div class="ppi-errors"><strong>Please review the payment details.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form class="panel ppi-card" method="POST" enctype="multipart/form-data" action="{{ route('admin.project-payments.receipts.store') }}">
        @csrf
        <div class="ppi-card-head"><div><span class="eyebrow">Payment details</span><h3>Receipt record</h3><p>Every receipt remains attached to its project and can optionally link to an invoice or staff contract.</p></div><span class="ppi-secure-badge">Private persistent storage</span></div>
        <div class="ppi-grid-2">
            <div class="field ppi-field-wide"><label for="project_id">Project</label><select id="project_id" name="project_id" required><option value="">Select project</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected((string) old('project_id', $selectedProject?->id) === (string) $project->id)>{{ $project->project_number }} · {{ $project->name }}</option>@endforeach</select></div>
            <div class="field"><label for="payment_type">Payment type</label><select id="payment_type" name="payment_type" required>@foreach ($paymentTypes as $value => $label)<option value="{{ $value }}" @selected(old('payment_type', $selectedStaffContract ? 'staff_part_payment' : 'project_part_payment') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="field"><label for="paid_on">Payment date</label><input id="paid_on" name="paid_on" type="date" value="{{ old('paid_on', now()->toDateString()) }}" required></div>
            <div class="field"><label for="amount">Amount paid</label><input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required></div>
            <div class="field"><label for="currency">Currency</label><input id="currency" name="currency" maxlength="3" value="{{ old('currency', config('project-payments.currency', 'USD')) }}" required></div>
            <div class="field"><label for="project_payment_invoice_id">Payment invoice <span style="color:var(--muted);font-weight:400">(optional)</span></label><select id="project_payment_invoice_id" name="project_payment_invoice_id"><option value="">Not linked to an invoice</option>@foreach ($projects as $project) @foreach ($project->paymentInvoices as $invoice)<option value="{{ $invoice->id }}" data-project="{{ $project->id }}" @selected((string) old('project_payment_invoice_id', $selectedInvoiceId) === (string) $invoice->id)>{{ $invoice->invoice_number }} · {{ $invoice->currency }} {{ number_format((float) $invoice->total_amount, 2) }}</option>@endforeach @endforeach</select></div>
            <div class="field" data-staff-contract-field><label for="staff_contract_id">Staff contract <span style="color:var(--muted);font-weight:400">(required for staff payments)</span></label><select id="staff_contract_id" name="staff_contract_id"><option value="">Select staff contract</option>@foreach ($projects as $project) @foreach ($project->staffContracts as $contract)<option value="{{ $contract->id }}" data-project="{{ $project->id }}" @selected((string) old('staff_contract_id', $selectedStaffContractId) === (string) $contract->id)>{{ $contract->contract_number }} · {{ $contract->staff_name }} · {{ $contract->staff_role }}</option>@endforeach @endforeach</select></div>
            <div class="field"><label for="counterparty">Paid to / received from</label><input id="counterparty" name="counterparty" value="{{ old('counterparty', $selectedStaffContract?->staff_name) }}" placeholder="Person or company"></div>
            <div class="field"><label for="reference">Payment reference</label><input id="reference" name="reference" value="{{ old('reference') }}" placeholder="Transfer or transaction ID"></div>
            <div class="field ppi-field-wide"><label for="notes">Internal note</label><textarea id="notes" name="notes" rows="4" placeholder="Optional context for reconciliation">{{ old('notes') }}</textarea></div>
            <div class="field ppi-field-wide"><label for="receipt">Receipt file</label><input class="ppi-file-input" id="receipt" name="receipt" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" required><small>PDF, JPG, PNG or WebP, up to 20 MB.</small></div>
        </div>
        <div class="ppi-upload-note" style="margin:18px 0">Receipt files are stored privately outside the application release directory, so they survive Git pushes and deployments. The database stores only the file path, original name, MIME type, and size—not the file contents.</div>
        <div class="ppi-actions"><button class="button" type="submit">Upload payment receipt</button><a class="ghost-button" href="{{ route('admin.project-payments.receipts.index') }}">Cancel</a></div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const project = document.getElementById('project_id');
    const type = document.getElementById('payment_type');
    const invoice = document.getElementById('project_payment_invoice_id');
    const contract = document.getElementById('staff_contract_id');

    const filterLinkedOptions = (select) => {
        if (!select) return;
        [...select.options].forEach((option, index) => {
            if (index === 0) return;
            option.hidden = option.dataset.project !== project.value;
            option.disabled = option.hidden;
        });
        if (select.selectedOptions[0]?.disabled) select.value = '';
    };
    const sync = () => {
        filterLinkedOptions(invoice);
        filterLinkedOptions(contract);
        if (contract) contract.required = type?.value.startsWith('staff_') ?? false;
    };
    project?.addEventListener('change', sync);
    type?.addEventListener('change', sync);
    sync();
});
</script>
@endsection
