@php
    $invoice = $invoice ?? null;
    $defaults = $defaults ?? [];
    $field = fn (string $key, mixed $fallback = '') => old($key, $invoice?->{$key} ?? data_get($defaults, $key, $fallback));
    $issueDate = old('issue_date', $invoice?->issue_date?->format('Y-m-d') ?? data_get($defaults, 'issue_date', now()->toDateString()));
    $dueDate = old('due_date', $invoice?->due_date?->format('Y-m-d') ?? data_get($defaults, 'due_date'));
    $selectedProjectId = (string) old('project_id', $invoice?->project_id ?? data_get($defaults, 'project_id', $selectedProject?->id));
@endphp

@if ($errors->any())
    <div class="ppi-errors" role="alert">
        <strong>Please correct the highlighted invoice details.</strong>
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" data-payment-invoice-form>
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="ppi-form-layout">
        <div class="ppi-stack">
            <section class="panel ppi-card">
                <div class="ppi-card-head"><div><span class="eyebrow">Pipeline source</span><h3>Choose the project and billing point</h3><p>Project details prefill the invoice and remain editable below.</p></div><span class="ppi-step">1</span></div>
                <div class="ppi-grid-2">
                    <div class="field ppi-field-wide">
                        <label for="project_id">Pipeline project</label>
                        <select id="project_id" name="project_id" required data-project-picker>
                            <option value="">Choose a project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected($selectedProjectId === (string) $project->id)>{{ $project->project_number }} · {{ $project->name }} ({{ $project->progress_percentage }}% complete)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="contract_value">Project contract value</label>
                        <input id="contract_value" name="contract_value" type="number" min="0.01" max="9999999999.99" step="0.01" value="{{ $field('contract_value') }}" required data-contract-value>
                    </div>
                    <div class="field">
                        <label for="completion_percentage">Payment / completion percentage</label>
                        <input id="completion_percentage" name="completion_percentage" type="number" min="0.01" max="100" step="0.01" value="{{ $field('completion_percentage', data_get($defaults, 'progress')) }}" required data-completion-percentage>
                        <div class="ppi-progress-note" data-project-progress-note>The selected project is {{ data_get($defaults, 'progress', $invoice?->completion_percentage ?? 0) }}% complete. You can adjust this billing percentage.</div>
                    </div>
                    <div class="field">
                        <label for="tax_rate">Tax / VAT rate (%)</label>
                        <input id="tax_rate" name="tax_rate" type="number" min="0" max="100" step="0.01" value="{{ $field('tax_rate', '0.00') }}" required data-tax-rate>
                    </div>
                    <div class="field">
                        <label for="exchange_rate">Exchange rate</label>
                        <input id="exchange_rate" name="exchange_rate" type="number" min="0.0001" step="0.0001" value="{{ $field('exchange_rate', config('project-payments.exchange_rate', 1370)) }}" required data-exchange-rate>
                    </div>
                    <div class="field">
                        <label for="currency">Invoice currency</label>
                        <input id="currency" name="currency" minlength="3" maxlength="3" value="{{ $field('currency', config('project-payments.currency', 'USD')) }}" required data-currency>
                    </div>
                    <div class="field">
                        <label for="local_currency">Local currency</label>
                        <input id="local_currency" name="local_currency" minlength="3" maxlength="3" value="{{ $field('local_currency', config('project-payments.local_currency', 'NGN')) }}" required data-local-currency>
                    </div>
                </div>
            </section>

            <section class="panel ppi-card">
                <div class="ppi-card-head"><div><span class="eyebrow">Client & project</span><h3>Edit the invoice narrative</h3><p>These values are saved as invoice snapshots, so later project edits will not rewrite them.</p></div><span class="ppi-step">2</span></div>
                <input type="hidden" name="original_invoice_id" value="{{ $field('original_invoice_id') }}" data-original-invoice-id>
                <div class="ppi-grid-2">
                    <div class="field"><label for="client_company">Client / company</label><input id="client_company" name="client_company" value="{{ $field('client_company') }}" required data-client-company></div>
                    <div class="field"><label for="client_name">Contact name</label><input id="client_name" name="client_name" value="{{ $field('client_name') }}" data-client-name></div>
                    <div class="field"><label for="client_title">Contact title</label><input id="client_title" name="client_title" value="{{ $field('client_title') }}" data-client-title></div>
                    <div class="field"><label for="client_email">Contact email</label><input id="client_email" name="client_email" type="email" value="{{ $field('client_email') }}" data-client-email></div>
                    <div class="field"><label for="client_phone">Contact phone</label><input id="client_phone" name="client_phone" value="{{ $field('client_phone') }}" data-client-phone></div>
                    <div class="field"><label for="project_category">Project category</label><input id="project_category" name="project_category" value="{{ $field('project_category') }}" data-project-category></div>
                    <div class="field ppi-field-wide"><label for="project_title">Project title</label><input id="project_title" name="project_title" value="{{ $field('project_title') }}" required data-project-title></div>
                    <div class="field ppi-field-wide"><label for="description">Payment request context</label><textarea id="description" name="description" rows="4" required data-description>{{ $field('description') }}</textarea></div>
                    <div class="field ppi-field-wide"><label for="payment_description">Payment line description</label><input id="payment_description" name="payment_description" value="{{ $field('payment_description') }}" required data-payment-description></div>
                </div>
            </section>

            <section class="panel ppi-card">
                <div class="ppi-card-head"><div><span class="eyebrow">Terms & remittance</span><h3>Set dates and payment details</h3><p>The bank details and closing note appear on the exported PDF.</p></div><span class="ppi-step">3</span></div>
                <div class="ppi-grid-3">
                    <div class="field"><label for="issue_date">Invoice date</label><input id="issue_date" name="issue_date" type="date" value="{{ $issueDate }}" required></div>
                    <div class="field"><label for="due_date">Due date</label><input id="due_date" name="due_date" type="date" value="{{ $dueDate }}"></div>
                    <div class="field"><label for="payment_terms">Payment terms</label><input id="payment_terms" name="payment_terms" value="{{ $field('payment_terms', 'Due upon receipt') }}" required></div>
                    <div class="field ppi-field-wide"><label for="original_invoice_number">Original invoice / contract reference</label><input id="original_invoice_number" name="original_invoice_number" value="{{ $field('original_invoice_number') }}" data-original-invoice-number></div>
                    <div class="field"><label for="bank_name">Bank</label><input id="bank_name" name="bank_name" value="{{ $field('bank_name', config('project-payments.bank_name')) }}"></div>
                    <div class="field"><label for="account_name">Account name</label><input id="account_name" name="account_name" value="{{ $field('account_name', config('project-payments.account_name')) }}"></div>
                    <div class="field"><label for="account_number">Account number</label><input id="account_number" name="account_number" value="{{ $field('account_number', config('project-payments.account_number')) }}"></div>
                    <div class="field"><label for="prepared_by">Prepared by</label><input id="prepared_by" name="prepared_by" value="{{ $field('prepared_by', config('luxury-quotes.brand.studio_name')) }}"></div>
                    <div class="field ppi-field-wide"><label for="notes">Closing note</label><textarea id="notes" name="notes" rows="2">{{ $field('notes', 'Thank you for your continued partnership.') }}</textarea></div>
                </div>
            </section>

            <div class="ppi-actions">
                <button class="button" type="submit" @disabled($projects->isEmpty())>{{ $submitLabel }}</button>
                <a class="ghost-button" href="{{ $cancelUrl }}">Cancel</a>
            </div>
        </div>

        <aside class="panel ppi-live-card" aria-live="polite">
            <div class="ppi-live-top"><span>Live calculation</span><strong data-summary-project>{{ $field('project_title', 'Choose a project') ?: 'Choose a project' }}</strong></div>
            <div class="ppi-live-body">
                <div class="ppi-live-row"><span>Contract value</span><strong data-summary-contract>—</strong></div>
                <div class="ppi-live-row"><span>Payment percentage</span><strong data-summary-percentage>—</strong></div>
                <div class="ppi-live-row"><span>Subtotal</span><strong data-summary-subtotal>—</strong></div>
                <div class="ppi-live-row"><span>Tax / VAT</span><strong data-summary-tax>—</strong></div>
                <div class="ppi-live-row"><span>Balance after payment</span><strong data-summary-balance>—</strong></div>
                <div class="ppi-live-total"><span>Total due</span><strong data-summary-total>—</strong><small data-summary-local>—</small></div>
            </div>
        </aside>
    </div>
</form>

@push('scripts')
<script>
(() => {
    const form = document.querySelector('[data-payment-invoice-form]');
    if (!form) return;

    const options = @json($projectOptions);
    const pick = (selector) => form.querySelector(selector);
    const projectPicker = pick('[data-project-picker]');
    const contract = pick('[data-contract-value]');
    const percentage = pick('[data-completion-percentage]');
    const taxRate = pick('[data-tax-rate]');
    const exchangeRate = pick('[data-exchange-rate]');
    const currency = pick('[data-currency]');
    const localCurrency = pick('[data-local-currency]');

    const money = (value, code) => {
        try {
            return new Intl.NumberFormat('en', { style: 'currency', currency: (code || 'USD').toUpperCase(), maximumFractionDigits: 2 }).format(value || 0);
        } catch (error) {
            return `${(code || '').toUpperCase()} ${Number(value || 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
        }
    };

    const recalculate = () => {
        const contractValue = Math.max(0, Number(contract.value) || 0);
        const percent = Math.min(100, Math.max(0, Number(percentage.value) || 0));
        const subtotal = contractValue * percent / 100;
        const tax = subtotal * Math.min(100, Math.max(0, Number(taxRate.value) || 0)) / 100;
        const total = subtotal + tax;
        const balance = Math.max(0, contractValue - subtotal);
        const currencyCode = currency.value || 'USD';

        pick('[data-summary-contract]').textContent = money(contractValue, currencyCode);
        pick('[data-summary-percentage]').textContent = `${percent.toLocaleString(undefined, { maximumFractionDigits: 2 })}%`;
        pick('[data-summary-subtotal]').textContent = money(subtotal, currencyCode);
        pick('[data-summary-tax]').textContent = money(tax, currencyCode);
        pick('[data-summary-balance]').textContent = money(balance, currencyCode);
        pick('[data-summary-total]').textContent = money(total, currencyCode);
        pick('[data-summary-local]').textContent = `${money(total * (Number(exchangeRate.value) || 0), localCurrency.value || 'NGN')} local equivalent`;
        pick('[data-summary-project]').textContent = pick('[data-project-title]').value || 'Choose a project';
    };

    const setValue = (selector, value) => {
        const input = pick(selector);
        if (input) input.value = value ?? '';
    };

    projectPicker?.addEventListener('change', () => {
        const project = options[String(projectPicker.value)];
        if (!project) return;

        setValue('[data-contract-value]', project.contract_value);
        setValue('[data-completion-percentage]', project.progress || '');
        setValue('[data-client-company]', project.client_company);
        setValue('[data-client-name]', project.client_name);
        setValue('[data-client-title]', project.client_title);
        setValue('[data-client-email]', project.client_email);
        setValue('[data-client-phone]', project.client_phone);
        setValue('[data-project-title]', project.project_title);
        setValue('[data-project-category]', project.project_category);
        setValue('[data-description]', project.description);
        setValue('[data-payment-description]', project.payment_description);
        setValue('[data-original-invoice-id]', project.original_invoice_id);
        setValue('[data-original-invoice-number]', project.original_invoice_number);
        setValue('[data-exchange-rate]', project.exchange_rate);
        pick('[data-project-progress-note]').textContent = `The selected project is ${project.progress}% complete. You can adjust this billing percentage.`;
        recalculate();
    });

    form.addEventListener('input', (event) => {
        if (event.target.matches('input, textarea, select')) recalculate();
    });
    recalculate();
})();
</script>
@endpush
