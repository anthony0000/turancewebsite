@extends('admin.layouts.app')

@php
    $editing = filled($contract);
    $pageTitle = $editing ? 'Edit staff contract' : 'Create an invoice-linked staff contract';
    $formAction = $editing ? route('admin.staff-contracts.update', $contract) : route('admin.staff-contracts.store');
    $selectedInvoice = old('luxury_quote_id', $contract?->luxury_quote_id);
    $defaultCurrency = old('currency', $contract?->currency ?? ($brand['currency'] ?? 'NGN'));
    $defaultCompany = old('company_name', $contract?->company_name ?? ($brand['studio_name'] ?? 'Turance Technologies'));
@endphp

@section('title', $pageTitle.' | Admin')

@section('content')
    <style>
        .contract-form {
            display: grid;
            gap: 24px;
        }

        .contract-form .form-section {
            gap: 18px;
        }

        .contract-form .section-copy {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .contract-form .rich-editor-body {
            counter-reset: staff-contract-editor-outline;
        }

        .contract-form .rich-editor-body > ol {
            padding-left: 0;
            list-style: none;
        }

        .contract-form .rich-editor-body > ol > li {
            position: relative;
            padding-left: 22px;
            counter-increment: staff-contract-editor-outline;
        }

        .contract-form .rich-editor-body > ol > li::before {
            position: absolute;
            top: 0;
            left: 0;
            content: counter(staff-contract-editor-outline) '. ';
            color: var(--text);
        }

        .contract-form .rich-editor-body > ol > li > p {
            margin-bottom: 8px;
        }

        .form-help {
            margin: -2px 0 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
        }

        .validation-summary {
            padding: 16px 18px;
            border: 1px solid rgba(185, 74, 61, 0.3);
            border-radius: 16px;
            background: rgba(185, 74, 61, 0.08);
            color: var(--danger);
        }

        .validation-summary ul {
            margin: 8px 0 0 18px;
        }

        @media (max-width: 720px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Staff contract builder</span>
            <h1>{{ $pageTitle }}.</h1>
            <p>
                Select the existing invoice, then capture the staff engagement, agreed price, working terms, and
                signatures in one document that can be reviewed and exported as a PDF.
            </p>
        </div>
        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Required relationship</span>
                <strong>One invoice</strong>
                <p>The saved contract is linked to an existing invoice and inherits its project reference.</p>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="validation-summary" style="margin-top: 24px;">
            <strong>Review the highlighted contract details.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="panel panel-padded contract-form" method="POST" action="{{ $formAction }}" enctype="multipart/form-data" style="margin-top: 24px;">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <section class="form-section">
            <div>
                <span class="eyebrow">01 / Invoice attachment</span>
                <h2>Choose the invoice this contract belongs to</h2>
                <p class="section-copy">The contract uses the invoice's project title and client details for its parent project.</p>
            </div>

            <div class="form-grid">
                <div class="field-full">
                    <label for="luxury_quote_id">Existing invoice</label>
                    <select id="luxury_quote_id" name="luxury_quote_id" required>
                        <option value="">Choose an invoice</option>
                        @foreach ($invoices as $invoice)
                            <option value="{{ $invoice->id }}" @selected((string) $selectedInvoice === (string) $invoice->id)>
                                {{ $invoice->quote_number }} - {{ $invoice->project_title }} / {{ $invoice->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($invoices->isEmpty())
                        <p class="form-help">No invoices are available yet. <a href="{{ route('admin.quotes.create') }}">Create an invoice first</a>, then return here to attach the contract.</p>
                    @else
                        <p class="form-help">Choose the invoice that already represents this project. The project reference is derived automatically.</p>
                    @endif
                </div>
            </div>
        </section>

        <section class="form-section">
            <div>
                <span class="eyebrow">02 / Staff engagement</span>
                <h2>Define the person and assignment</h2>
                <p class="section-copy">Capture the person and their responsibility on the project.</p>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label for="staff_name">Staff name</label>
                    <input id="staff_name" type="text" name="staff_name" value="{{ old('staff_name', $contract?->staff_name) }}" required maxlength="255">
                </div>
                <div class="field">
                    <label for="staff_role">Role on project</label>
                    <input id="staff_role" type="text" name="staff_role" value="{{ old('staff_role', $contract?->staff_role) }}" required maxlength="255" placeholder="e.g. Product Designer">
                </div>
                <div class="field">
                    <label for="status">Contract status</label>
                    <select id="status" name="status" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $contract?->status ?? 'draft') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        <section class="form-section">
            <div>
                <span class="eyebrow">03 / Price</span>
                <h2>Record the commercial agreement</h2>
                <p class="section-copy">The agreed fee and payment terms appear together in the contract document.</p>
            </div>
            <div class="form-grid">
                <input type="hidden" name="currency" value="{{ $defaultCurrency }}">
                <div class="field">
                    <label for="agreed_fee">Agreed fee</label>
                    <input id="agreed_fee" type="number" name="agreed_fee" value="{{ old('agreed_fee', $contract?->agreed_fee) }}" required min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="field-full">
                    <label for="payment_terms">Payment terms</label>
                    <textarea id="payment_terms" name="payment_terms" required maxlength="5000" placeholder="e.g. 50% on signing and 50% after final project handoff.">{{ old('payment_terms', $contract?->payment_terms) }}</textarea>
                </div>
            </div>
        </section>

        <section class="form-section">
            <div>
                <span class="eyebrow">04 / Terms</span>
                <h2>Set the work and operating terms</h2>
                <p class="section-copy">Write the practical terms that the staff member and company will review before signing.</p>
            </div>
            <div class="form-grid">
                <div class="field-full">
                    <label for="scope_of_work">Scope of work</label>
                    <textarea id="scope_of_work" name="scope_of_work" required maxlength="15000" data-rich-editor placeholder="Describe the responsibilities, deliverables, and project expectations.">{{ old('scope_of_work', $contract?->scope_of_work) }}</textarea>
                </div>
                <div class="field-full">
                    <label for="terms">Terms and conditions</label>
                    <textarea id="terms" name="terms" required maxlength="20000" data-rich-editor placeholder="Include confidentiality, ownership, communication, termination, and other relevant terms.">{{ old('terms', $contract?->terms) }}</textarea>
                </div>
            </div>
        </section>

        <input type="hidden" name="company_name" value="{{ $defaultCompany }}">

        <section class="form-section">
            <div>
                <span class="eyebrow">05 / Signed proof</span>
                <h2>Keep the signed copy with this contract</h2>
                <p class="section-copy">Upload the signed agreement or supporting proof so it stays attached to the project record. The file is stored privately and is available only to authorised staff-contract admins.</p>
            </div>
            <div class="form-grid">
                <div class="field-full">
                    <label for="signed_document">Signed document</label>
                    <input id="signed_document" type="file" name="signed_document" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
                    <p class="form-help">Optional. PDF, Word document, or image up to 20 MB. Uploading a new file replaces the current proof copy.</p>
                    @if ($editing && $contract->hasSignedDocument())
                        <p class="form-help">Current file: <a href="{{ route('admin.staff-contracts.signed-document', $contract) }}">{{ $contract->signed_document_original_name ?: 'Download current signed document' }}</a></p>
                    @endif
                </div>
            </div>
        </section>

        <div class="wizard-actions">
            <a class="ghost-button" href="{{ $editing ? route('admin.staff-contracts.show', $contract) : route('admin.staff-contracts.index') }}">Cancel</a>
            <button class="button" type="submit">{{ $editing ? 'Save Contract Changes' : 'Create Staff Contract' }}</button>
        </div>
    </form>
@endsection
