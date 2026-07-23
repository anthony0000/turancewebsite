@extends('admin.layouts.app')

@section('title', $quote->quote_number.' | Invoice Preview')

@section('content')
    @php
        $mouNumber = str_replace('-INV-', '-MOU-', $quote->quote_number);
        $mouNumber = $mouNumber !== $quote->quote_number ? $mouNumber : 'MOU-'.$quote->quote_number;
        $exchangeRate = max(1, (float) ($quote->exchange_rate ?? 1370));
        $nairaInvestment = 'NGN '.number_format((float) $quote->investment_amount * $exchangeRate, 0);
    @endphp

    <section class="panel hero-banner">
        <div>
            <span class="eyebrow">Invoice Preview</span>
            <h1>{{ $quote->company_name }} invoice ready for review, PDF export, and MOU generation.</h1>
            <p>
                This preview shows the saved invoice sheet before download. Because the invoice has been generated, the
                contract MOU can now be exported from the same approved record.
            </p>
            <div class="hero-actions">
                <a class="button" href="{{ route('admin.quotes.pdf', $quote) }}">Download PDF</a>
                <a class="button" href="{{ route('admin.quotes.mou', $quote) }}">Download MOU</a>
                <a class="ghost-button" href="{{ route('admin.quotes.edit', $quote) }}">Edit Details</a>
                <a class="ghost-button" href="{{ route('admin.quotes.create') }}">Create Another Invoice</a>
            </div>
        </div>

        <div class="hero-callout">
            <div class="callout-card">
                <span class="metric-label">Layout</span>
                <strong>{{ $template['name'] ?? ucfirst($quote->template) }}</strong>
                <p>{{ $template['description'] ?? 'Invoice document layout.' }}</p>
            </div>
            <div class="callout-card">
                <span class="metric-label">Investment</span>
                <strong>${{ number_format((float) $quote->investment_amount, 0) }}</strong>
                <p>{{ $nairaInvestment }} at $1 = NGN {{ number_format($exchangeRate, 2) }}</p>
                <p>{{ $quote->timeline }} timeline / valid through {{ optional($quote->valid_until)->format('M d, Y') }}</p>
            </div>
        </div>
    </section>

    <div class="preview-grid">
        <section class="panel document-stage">
            <div class="document-frame">
                @include('admin.quotes.partials.document', [
                    'quote' => $quote,
                    'template' => $template,
                    'brand' => $brand,
                ])
            </div>
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Invoice Snapshot</span>
                <h3 class="panel-title">Commercial summary</h3>
                <div class="meta-list" style="margin-top: 18px;">
                    <div class="meta-item">
                        <span>Invoice Number</span>
                        <strong>{{ $quote->quote_number }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>MOU Number</span>
                        <strong>{{ $mouNumber }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Prepared For</span>
                        <strong>{{ $quote->company_name }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Project Category</span>
                        <strong>{{ $quote->project_category }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Created</span>
                        <strong>{{ optional($quote->created_at)->format('M d, Y') }}</strong>
                    </div>
                    <div class="meta-item">
                        <span>Last Updated</span>
                        <strong>{{ optional($quote->updated_at)->format('M d, Y') }}</strong>
                    </div>
                </div>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Included Sections</span>
                <h3 class="panel-title">Invoice structure</h3>
                <ul class="stack-list" style="margin-top: 18px;">
                    <li>
                        <strong>{{ count($quote->scope_items ?? []) }} scope items</strong>
                        <span>Clear delivery inclusions shaped for premium positioning.</span>
                    </li>
                    <li>
                        <strong>{{ count($quote->outcomes ?? []) }} expected outcomes</strong>
                        <span>Commercial and perception-driven results for the client.</span>
                    </li>
                    <li>
                        <strong>{{ count($quote->optional_addons ?? []) }} optional add-ons</strong>
                        <span>Extra services the client can layer into the engagement.</span>
                    </li>
                </ul>
            </section>
        </aside>
    </div>
@endsection
