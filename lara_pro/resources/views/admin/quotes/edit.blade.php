@extends('admin.layouts.app')

@section('title', 'Edit '.$quote->quote_number.' | Invoice Generator')

@section('content')
    @php
        $selectedTemplate = old('template', $quote->template);
        $selectedCategory = old('project_category', $quote->project_category);
        $lineItemsValue = old('line_items', $lineItems ?? []);
        $exchangeRateValue = old('exchange_rate', $quote->exchange_rate ?? ($defaultExchangeRate ?? 1370));
        $outcomesValue = old('outcomes', implode(PHP_EOL, $quote->outcomes ?? []));
        $milestonesValue = old('milestones', implode(PHP_EOL, $quote->milestones ?? []));
        $addonsValue = old('optional_addons', implode(PHP_EOL, $quote->optional_addons ?? []));
    @endphp

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Edit Invoice</span>
            <h1>Update {{ $quote->company_name }} and regenerate the saved invoice.</h1>
            <p>
                Adjust the commercial details, delivery scope, and final messaging below. Saving returns you to the
                preview, where the PDF and MOU downloads will use the updated invoice record.
            </p>
            <div class="hero-actions">
                <a class="ghost-button" href="{{ route('admin.quotes.show', $quote) }}">Back to Preview</a>
                <a class="ghost-button" href="{{ route('admin.quotes.pdf', $quote) }}">Download Current PDF</a>
                <a class="ghost-button" href="{{ route('admin.quotes.mou', $quote) }}">Download Current MOU</a>
            </div>
        </div>

        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Invoice Number</span>
                <strong>{{ $quote->quote_number }}</strong>
                <p>The reference stays the same while the details are updated.</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Last Updated</span>
                <strong>{{ optional($quote->updated_at)->format('M d, Y') }}</strong>
                <p>Save changes to refresh the preview and regenerate the PDF.</p>
            </div>
        </div>
    </section>

    <div class="dashboard-grid">
        <section class="panel panel-padded">
            <div class="panel-head">
                <span class="eyebrow">Invoice Editor</span>
                <h2>Edit invoice details</h2>
                <p>Every field below feeds directly into the saved preview and regenerated PDF export.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="quote-edit-form" method="POST" action="{{ route('admin.quotes.update', $quote) }}">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <span class="eyebrow">Project Brief</span>
                    <div class="form-grid">
                        <div class="field-full">
                            <label>Invoice Template</label>
                            <div class="template-grid">
                                @foreach ($templates as $key => $template)
                                    <label class="template-card">
                                        <input type="radio" name="template" value="{{ $key }}"
                                            {{ $selectedTemplate === $key ? 'checked' : '' }} required>
                                        <span class="eyebrow" style="margin: 0;">{{ $template['badge'] }}</span>
                                        <strong>{{ $template['name'] }}</strong>
                                        <p>{{ $template['description'] }}</p>
                                        <div class="swatch-row">
                                            <span class="swatch" style="background: {{ $template['palette']['page'] }}"></span>
                                            <span class="swatch" style="background: {{ $template['palette']['surface'] }}"></span>
                                            <span class="swatch" style="background: {{ $template['palette']['accent'] }}"></span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="field">
                            <label for="project_category">Project Category</label>
                            <select id="project_category" name="project_category" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" {{ $selectedCategory === $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field">
                            <label for="project_title">Project Title</label>
                            <input id="project_title" type="text" name="project_title"
                                value="{{ old('project_title', $quote->project_title) }}" required>
                        </div>

                        <div class="field-full">
                            <label for="executive_summary">Executive Summary</label>
                            <textarea id="executive_summary" name="executive_summary" required data-rich-editor>{{ old('executive_summary', $quote->executive_summary) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <span class="eyebrow">Client and Commercials</span>
                    <div class="form-grid">
                        <div class="field">
                            <label for="company_name">Company Name</label>
                            <input id="company_name" type="text" name="company_name"
                                value="{{ old('company_name', $quote->company_name) }}" required>
                        </div>

                        <div class="field">
                            <label for="company_industry">Industry or Market</label>
                            <input id="company_industry" type="text" name="company_industry"
                                value="{{ old('company_industry', $quote->company_industry) }}">
                        </div>

                        <div class="field">
                            <label for="recipient_name">Recipient Name</label>
                            <input id="recipient_name" type="text" name="recipient_name"
                                value="{{ old('recipient_name', $quote->recipient_name) }}">
                        </div>

                        <div class="field">
                            <label for="recipient_title">Recipient Title</label>
                            <input id="recipient_title" type="text" name="recipient_title"
                                value="{{ old('recipient_title', $quote->recipient_title) }}">
                        </div>

                        <div class="field">
                            <label for="recipient_email">Recipient Email</label>
                            <input id="recipient_email" type="email" name="recipient_email"
                                value="{{ old('recipient_email', $quote->recipient_email) }}">
                        </div>

                        <div class="field">
                            <label for="recipient_phone">Recipient Phone</label>
                            <input id="recipient_phone" type="text" name="recipient_phone"
                                value="{{ old('recipient_phone', $quote->recipient_phone) }}">
                        </div>

                        <div class="field">
                            <label for="timeline">Timeline</label>
                            <input id="timeline" type="text" name="timeline"
                                value="{{ old('timeline', $quote->timeline) }}" required>
                        </div>

                        <div class="field">
                            <label for="valid_until">Invoice Valid Until</label>
                            <input id="valid_until" type="date" name="valid_until"
                                value="{{ old('valid_until', optional($quote->valid_until)->toDateString()) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <span class="eyebrow">Scope and Delivery</span>
                    <div class="form-grid">
                        @include('admin.quotes.partials.line-items-editor', [
                            'lineItems' => $lineItemsValue,
                            'priceBounds' => $priceBounds,
                            'exchangeRate' => $exchangeRateValue,
                        ])

                        <div class="field-full">
                            <label for="outcomes">Expected Outcomes</label>
                            <textarea id="outcomes" name="outcomes" data-rich-editor>{{ $outcomesValue }}</textarea>
                        </div>

                        <div class="field-full">
                            <label for="milestones">Delivery Milestones</label>
                            <textarea id="milestones" name="milestones" data-rich-editor>{{ $milestonesValue }}</textarea>
                        </div>

                        <div class="field-full">
                            <label for="optional_addons">Optional Add-ons</label>
                            <textarea id="optional_addons" name="optional_addons" data-rich-editor>{{ $addonsValue }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <span class="eyebrow">Final Messaging</span>
                    <div class="form-grid">
                        <div class="field-full">
                            <label for="intro_message">Opening Message</label>
                            <textarea id="intro_message" name="intro_message" data-rich-editor>{{ old('intro_message', $quote->intro_message) }}</textarea>
                        </div>

                        <div class="field-full">
                            <label for="closing_note">Closing Note</label>
                            <textarea id="closing_note" name="closing_note" data-rich-editor>{{ old('closing_note', $quote->closing_note) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="wizard-actions">
                    <span class="admin-pill">Save changes to regenerate preview</span>
                    <div class="wizard-actions-group">
                        <a class="ghost-button" href="{{ route('admin.quotes.show', $quote) }}">Cancel</a>
                        <button type="submit" class="button">Save and Regenerate</button>
                    </div>
                </div>
            </form>
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Regeneration Flow</span>
                <h3 class="panel-title">What happens after saving</h3>
                <ul class="stack-list" style="margin-top: 18px;">
                    <li>
                        <strong>Details are updated</strong>
                        <span>The existing invoice record is saved with the new values.</span>
                    </li>
                    <li>
                        <strong>Preview refreshes</strong>
                        <span>You return to the invoice preview using the edited details.</span>
                    </li>
                    <li>
                        <strong>PDF and MOU regenerate</strong>
                        <span>Download either document again to export the updated invoice or contract MOU.</span>
                    </li>
                </ul>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Current Snapshot</span>
                <h3 class="panel-title">Saved invoice</h3>
                <div class="meta-list" style="margin-top: 18px;">
                    <div class="meta-item">
                        <span>Invoice Number</span>
                        <strong>{{ $quote->quote_number }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Company</span>
                        <strong>{{ $quote->company_name }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Investment</span>
                        <strong>${{ number_format((float) $quote->investment_amount, 0) }}</strong>
                        <p>NGN {{ number_format((float) $quote->investment_amount * (float) ($quote->exchange_rate ?? 1370), 0) }}</p>
                    </div>
                    <div class="meta-item">
                        <span>Valid Until</span>
                        <strong>{{ optional($quote->valid_until)->format('M d, Y') }}</strong>
                    </div>
                </div>
            </section>
        </aside>
    </div>
@endsection
