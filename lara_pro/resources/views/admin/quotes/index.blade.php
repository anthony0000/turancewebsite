@extends('admin.layouts.app')

@section('title', match (request()->route()?->getName()) {
    'admin.quotes.activity' => 'Activity | Admin',
    'admin.quotes.insights' => 'Insights | Admin',
    'admin.quotes.create' => 'Invoice Builder | Admin',
    'admin.quotes.archive' => 'Invoice Archive | Admin',
    default => 'Dashboard | Admin',
})

@section('content')
    @php
        $defaultTemplate = old('template', array_key_first($templates));
        $selectedCategory = old('project_category', $categories[0] ?? null);
        $lineItemsDefault = old('line_items', $defaultLineItems ?? []);
        $exchangeRateDefault = old('exchange_rate', $defaultExchangeRate ?? 1370);
        $outcomesDefault = old('outcomes', implode(PHP_EOL, $defaults['outcomes'] ?? []));
        $milestonesDefault = old('milestones', implode(PHP_EOL, $defaults['milestones'] ?? []));
        $addonsDefault = old('optional_addons', implode(PHP_EOL, $defaults['optional_addons'] ?? []));
        $introDefault = old(
            'intro_message',
            'We have prepared this invoice to help your company move forward with clarity, confidence, and premium execution across the digital experience.'
        );
        $closingDefault = old(
            'closing_note',
            'Once approved, we move into alignment, lock the delivery roadmap, and execute the engagement with focused polish from first payment to final handoff.'
        );
        $wizardSteps = [
            [
                'id' => 'brief',
                'label' => 'Project Brief',
                'description' => 'Template and category',
                'fields' => ['template', 'project_category', 'project_title', 'executive_summary'],
            ],
            [
                'id' => 'client',
                'label' => 'Client',
                'description' => 'Identity and timeline',
                'fields' => [
                    'company_name',
                    'company_industry',
                    'recipient_name',
                    'recipient_title',
                    'recipient_email',
                    'recipient_phone',
                    'timeline',
                    'valid_until',
                ],
            ],
            [
                'id' => 'delivery',
                'label' => 'Scope',
                'description' => 'Costs and outcomes',
                'fields' => ['line_items', 'outcomes', 'milestones', 'optional_addons'],
            ],
            [
                'id' => 'finish',
                'label' => 'Review',
                'description' => 'Notes and submit',
                'fields' => ['intro_message', 'closing_note'],
            ],
        ];
        $initialWizardStep = 0;
        $errorKeys = $errors->keys();

        foreach ($wizardSteps as $index => $step) {
            foreach ($step['fields'] as $field) {
                foreach ($errorKeys as $errorKey) {
                    if ($errorKey === $field || str_starts_with($errorKey, $field.'.')) {
                        $initialWizardStep = $index;
                        break 3;
                    }
                }

                if ($errors->has($field)) {
                    $initialWizardStep = $index;
                    break 2;
                }
            }
        }

        $adminSection = $adminSection ?? 'overview';
        $showOverview = $adminSection === 'overview';
        $showActivity = $adminSection === 'activity';
        $showInsights = $adminSection === 'insights';
        $showBuilder = $adminSection === 'builder';
        $showArchive = $adminSection === 'archive';
    @endphp

    @if ($showOverview)
    <section class="dashboard-command" id="dashboard-overview">
        <div class="dashboard-command-main">
            <span class="eyebrow">Command Center</span>
            <h2>Executive overview for today's admin work.</h2>
            <p>Start with the key numbers, then move into a focused workspace when you need more detail.</p>

            <div class="dashboard-command-actions">
                <a class="button" href="{{ route('admin.quotes.create') }}">Create Invoice</a>
                @if ($quotes->isNotEmpty())
                    <a class="ghost-button" href="{{ route('admin.quotes.show', $quotes->first()) }}">Open Latest</a>
                @endif
                <a class="ghost-button" href="{{ route('admin.quotes.activity') }}">View Activity</a>
            </div>
        </div>

        <div class="dashboard-status-grid">
            <div class="status-card">
                <span class="metric-label">Tracking</span>
                <strong>{{ $visitTrackingReady ? 'Live' : 'Pending setup' }}</strong>
                <p>
                    {{ $visitTrackingReady
                        ? 'Public page visits are being captured.'
                        : 'Run the page visits migration to enable traffic data.' }}
                </p>
            </div>

            <div class="status-card">
                <span class="metric-label">This Month</span>
                <strong>{{ number_format($quotesThisMonth) }} invoices / {{ number_format($messagesThisMonth) }} leads</strong>
                <p>{{ number_format($visitsThisMonth) }} tracked visits.</p>
            </div>
        </div>
    </section>

    <section class="kpi-grid">
        @foreach ($kpiCards as $card)
            <article class="kpi-card kpi-card--{{ $card['tone'] }}">
                <div class="kpi-top">
                    <span class="metric-label">{{ $card['label'] }}</span>
                    <span class="trend-pill trend-pill--{{ $card['trend']['direction'] }}">{{ $card['trend']['label'] }}</span>
                </div>

                <strong class="kpi-value">{{ $card['value'] }}</strong>
                <p class="panel-copy">{{ $card['hint'] }}</p>
                <span class="kpi-context">{{ $card['trend']['context'] }}</span>
            </article>
        @endforeach
    </section>

    <div class="dashboard-grid">
        <section class="panel panel-padded">
            <div class="panel-head">
                <span class="eyebrow">Operating Priorities</span>
                <h2>What needs attention</h2>
                <p>A quick read before opening a detailed workspace.</p>
            </div>

            <div class="bar-list">
                @foreach ($dashboardHighlights as $highlight)
                    <div class="bar-row">
                        <div class="bar-header">
                            <div>
                                <strong>{{ $highlight['value'] }}</strong>
                                <span class="bar-meta">{{ $highlight['label'] }}</span>
                            </div>
                        </div>
                        <span class="bar-meta">{{ $highlight['meta'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Shortcuts</span>
                <h3 class="panel-title">Open a workspace</h3>
                <div class="workspace-link-grid" style="margin-top: 14px;">
                    <a href="{{ route('admin.quotes.activity') }}">Activity Center<span>Traffic, leads, invoices</span></a>
                    <a href="{{ route('admin.quotes.insights') }}">Business Insights<span>Patterns and pipeline</span></a>
                    <a href="{{ route('admin.quotes.create') }}">Invoice Builder<span>Guided creation flow</span></a>
                    <a href="{{ route('admin.quotes.archive') }}">Invoice Archive<span>Saved documents</span></a>
                </div>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Latest Invoice</span>
                <h3 class="panel-title">Recent output</h3>
                <ul class="mini-list" style="margin-top: 14px;">
                    @forelse ($quotes->take(4) as $quote)
                        <li>
                            <strong>{{ $quote->company_name }}</strong>
                            <span>{{ $quote->quote_number }} / ${{ number_format((float) $quote->investment_amount, 0) }}</span>
                        </li>
                    @empty
                        <li>
                            <strong>No invoices yet</strong>
                            <span>Create one from the invoice builder.</span>
                        </li>
                    @endforelse
                </ul>
            </section>
        </aside>
    </div>
    @endif

    @if ($showActivity)
    <div class="section-heading" id="performance-overview">
        <div>
            <span class="eyebrow">Performance</span>
            <h2>Activity at a glance</h2>
            <p>Recent traffic, leads, and invoice output.</p>
        </div>
        <span class="admin-pill">Live view</span>
    </div>

    <section class="kpi-grid">
        @foreach ($kpiCards as $card)
            <article class="kpi-card kpi-card--{{ $card['tone'] }}">
                <div class="kpi-top">
                    <span class="metric-label">{{ $card['label'] }}</span>
                    <span class="trend-pill trend-pill--{{ $card['trend']['direction'] }}">{{ $card['trend']['label'] }}</span>
                </div>

                <strong class="kpi-value">{{ $card['value'] }}</strong>
                <p class="panel-copy">{{ $card['hint'] }}</p>
                <span class="kpi-context">{{ $card['trend']['context'] }}</span>
            </article>
        @endforeach
    </section>

    <div class="analytics-grid">
        <section class="panel panel-padded">
            <div class="panel-head">
                <span class="eyebrow">Activity</span>
                <h2>14-day snapshot</h2>
                <p>Visits, leads, and invoices over the same window.</p>
            </div>

            <div class="chart-legend">
                <span class="legend-item">
                    <span class="legend-swatch legend-swatch--visits"></span>
                    Visits
                </span>
                <span class="legend-item">
                    <span class="legend-swatch legend-swatch--quotes"></span>
                    Invoices
                </span>
                <span class="legend-item">
                    <span class="legend-swatch legend-swatch--messages"></span>
                    Leads
                </span>
            </div>

            <div class="line-chart-shell">
                <svg class="line-chart" viewBox="0 0 640 220" role="img" aria-label="Fourteen day activity chart">
                    <line class="chart-grid-line" x1="18" y1="18" x2="622" y2="18" />
                    <line class="chart-grid-line" x1="18" y1="73" x2="622" y2="73" />
                    <line class="chart-grid-line" x1="18" y1="128" x2="622" y2="128" />
                    <line class="chart-grid-line" x1="18" y1="183" x2="622" y2="183" />
                    <polyline class="chart-line chart-line--visits" points="{{ $dailyOverview['visit_points'] }}" />
                    <polyline class="chart-line chart-line--quotes" points="{{ $dailyOverview['quote_points'] }}" />
                    <polyline class="chart-line chart-line--messages" points="{{ $dailyOverview['message_points'] }}" />
                </svg>
            </div>

            <div class="chart-summary-grid">
                <div class="mini-card">
                    <span class="metric-label">14-Day Visits</span>
                    <strong>{{ number_format($dailyOverview['totals']['visits']) }}</strong>
                    <p>Tracked page views.</p>
                </div>
                <div class="mini-card">
                    <span class="metric-label">14-Day Invoices</span>
                    <strong>{{ number_format($dailyOverview['totals']['quotes']) }}</strong>
                    <p>Generated invoices.</p>
                </div>
                <div class="mini-card">
                    <span class="metric-label">14-Day Leads</span>
                    <strong>{{ number_format($dailyOverview['totals']['messages']) }}</strong>
                    <p>Contact enquiries.</p>
                </div>
                <div class="mini-card">
                    <span class="metric-label">Peak Traffic Day</span>
                    <strong>{{ $dailyOverview['peak']['full_label'] ?? 'No traffic yet' }}</strong>
                    <p>
                        {{ isset($dailyOverview['peak']['visits'])
                            ? number_format($dailyOverview['peak']['visits']).' visits'
                            : 'Waiting for traffic.' }}
                    </p>
                </div>
            </div>

            @if (! $visitTrackingReady)
                <div class="data-note" style="margin-top: 18px;">
                    Visit analytics will populate after the migration runs and new page views arrive.
                </div>
            @endif
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Signals</span>
                <h3 class="panel-title">Current movement</h3>

                <div class="bar-list" style="margin-top: 18px;">
                    @foreach ($dashboardHighlights as $highlight)
                        <div class="bar-row">
                            <div class="bar-header">
                                <div>
                                    <strong>{{ $highlight['value'] }}</strong>
                                    <span class="bar-meta">{{ $highlight['label'] }}</span>
                                </div>
                            </div>
                            <span class="bar-meta">{{ $highlight['meta'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Top Pages</span>
                <h3 class="panel-title">Last 30 days</h3>

                @if ($topPages !== [])
                    <div class="bar-list" style="margin-top: 18px;">
                        @foreach ($topPages as $page)
                            <div class="bar-row">
                                <div class="bar-header">
                                    <div>
                                        <strong>{{ $page['label'] }}</strong>
                                        <span class="bar-meta">{{ $page['meta'] }}</span>
                                    </div>
                                    <strong>{{ number_format($page['count']) }}</strong>
                                </div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ max(10, $page['width']) }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="data-note" style="margin-top: 18px;">
                        No page-visit records yet.
                    </div>
                @endif
            </section>
        </aside>
    </div>
    @endif

    @if ($showInsights)
    <div class="section-heading" id="business-insights">
        <div>
            <span class="eyebrow">Business Insights</span>
            <h2>Patterns behind the pipeline</h2>
            <p>Template demand, category focus, and monthly value trends.</p>
        </div>
        <span class="admin-pill">Decision view</span>
    </div>

    <div class="insight-grid" id="business-insights">
        <section class="panel panel-padded">
            <span class="eyebrow">Templates</span>
            <h3 class="panel-title">Most used styles</h3>

            @if ($templateBreakdown !== [])
                <div class="bar-list" style="margin-top: 18px;">
                    @foreach ($templateBreakdown as $template)
                        <div class="bar-row">
                            <div class="bar-header">
                                <div>
                                    <strong>{{ $template['label'] }}</strong>
                                    <span class="bar-meta">{{ $template['meta'] }}</span>
                                </div>
                                <strong>{{ number_format($template['count']) }}</strong>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill bar-fill--quote"
                                    style="width: {{ max(10, $template['width']) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-note" style="margin-top: 18px;">
                    Template rankings appear after invoices are stored.
                </div>
            @endif
        </section>

        <section class="panel panel-padded">
            <span class="eyebrow">Categories</span>
            <h3 class="panel-title">Demand mix</h3>

            @if ($categoryBreakdown !== [])
                <div class="bar-list" style="margin-top: 18px;">
                    @foreach ($categoryBreakdown as $category)
                        <div class="bar-row">
                            <div class="bar-header">
                                <div>
                                    <strong>{{ $category['label'] }}</strong>
                                    <span class="bar-meta">{{ $category['meta'] }}</span>
                                </div>
                                <strong>{{ number_format($category['count']) }}</strong>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill bar-fill--lead"
                                    style="width: {{ max(10, $category['width']) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-note" style="margin-top: 18px;">
                    Category data appears after invoices are stored.
                </div>
            @endif
        </section>

        <section class="panel panel-padded">
            <span class="eyebrow">Pipeline</span>
            <h3 class="panel-title">Monthly value</h3>

            @if ($monthlyPipeline !== [])
                <div class="mini-chart">
                    @foreach ($monthlyPipeline as $month)
                        <div class="month-bar">
                            <span>{{ $month['formatted_total'] }}</span>
                            <div class="month-bar-column" style="height: {{ max(16, $month['height'] * 1.7) }}px;"></div>
                            <strong>{{ $month['label'] }}</strong>
                            <span>{{ number_format($month['count']) }} invoices</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-note" style="margin-top: 18px;">
                    Pipeline bars appear after invoice values accumulate.
                </div>
            @endif
        </section>
    </div>
    @endif

    @if ($showBuilder)
    <div class="section-heading" id="invoice-studio">
        <div>
            <span class="eyebrow">Invoice Studio</span>
            <h2>Guided invoice builder</h2>
            <p>Four compact steps for brief, client, scope, and review.</p>
        </div>
        <span class="admin-pill">4 steps</span>
    </div>

    <div class="dashboard-grid">
        <section class="panel panel-padded" id="quote-builder">
            <div class="panel-head">
                <span class="eyebrow">New Invoice</span>
                <h2>Create invoice</h2>
                <p>Add the essentials, review the snapshot, and generate the saved PDF.</p>
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

            <div class="quote-wizard" data-quote-wizard data-initial-step="{{ $initialWizardStep }}">
                <div class="wizard-progress" role="tablist" aria-label="Invoice builder steps">
                    @foreach ($wizardSteps as $index => $step)
                        <button type="button"
                            class="wizard-progress-button {{ $index === $initialWizardStep ? 'is-active' : '' }}"
                            data-wizard-step-button data-step-index="{{ $index }}" role="tab"
                            aria-selected="{{ $index === $initialWizardStep ? 'true' : 'false' }}"
                            aria-controls="wizard-panel-{{ $step['id'] }}" id="wizard-tab-{{ $step['id'] }}">
                            <span class="wizard-progress-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="wizard-progress-copy">
                                <strong>{{ $step['label'] }}</strong>
                                <span>{{ $step['description'] }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.quotes.store') }}">
                    @csrf

                    <section class="wizard-pane {{ $initialWizardStep === 0 ? 'is-active' : '' }}" data-wizard-pane
                        data-step-index="0" id="wizard-panel-brief" role="tabpanel"
                        aria-labelledby="wizard-tab-brief">
                        <div class="wizard-pane-grid">
                            <div class="field-full">
                                <label>Luxury Template</label>
                                <div class="template-grid">
                                    @foreach ($templates as $key => $template)
                                        <label class="template-card">
                                            <input type="radio" name="template" value="{{ $key }}"
                                                {{ $defaultTemplate === $key ? 'checked' : '' }} required>
                                            <span class="eyebrow" style="margin: 0;">{{ $template['badge'] }}</span>
                                            <strong>{{ $template['name'] }}</strong>
                                            <p>{{ $template['description'] }}</p>
                                            <div class="swatch-row">
                                                <span class="swatch"
                                                    style="background: {{ $template['palette']['page'] }}"></span>
                                                <span class="swatch"
                                                    style="background: {{ $template['palette']['surface'] }}"></span>
                                                <span class="swatch"
                                                    style="background: {{ $template['palette']['accent'] }}"></span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="field">
                                <label for="project_category">Project Category</label>
                                <select id="project_category" name="project_category" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}"
                                            {{ $selectedCategory === $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="wizard-note">
                                <strong>Set the frame</strong>
                                <p>Template and category shape the final PDF.</p>
                            </div>

                            <div class="field-full">
                                <label for="project_title">Project Title</label>
                                <input id="project_title" type="text" name="project_title"
                                    value="{{ old('project_title') }}"
                                    placeholder="Luxury Website and Brand Presentation Upgrade" required>
                            </div>

                            <div class="field-full">
                                <label for="executive_summary">Executive Summary</label>
                                <textarea id="executive_summary" name="executive_summary" required data-rich-editor
                                    placeholder="Summarize the project direction and business need.">{{ old('executive_summary') }}</textarea>
                            </div>
                        </div>

                        <div class="wizard-actions">
                            <span class="admin-pill">Step 1 of 4</span>
                            <div class="wizard-actions-group">
                                <button type="button" class="button" data-wizard-next>Continue</button>
                            </div>
                        </div>
                    </section>

                    <section class="wizard-pane {{ $initialWizardStep === 1 ? 'is-active' : '' }}" data-wizard-pane
                        data-step-index="1" id="wizard-panel-client" role="tabpanel"
                        aria-labelledby="wizard-tab-client">
                        <div class="wizard-pane-grid">
                            <div class="field">
                                <label for="company_name">Company Name</label>
                                <input id="company_name" type="text" name="company_name"
                                    value="{{ old('company_name') }}" placeholder="Asterion Holdings" required>
                            </div>

                            <div class="field">
                                <label for="company_industry">Industry or Market</label>
                                <input id="company_industry" type="text" name="company_industry"
                                    value="{{ old('company_industry') }}"
                                    placeholder="Luxury real estate, fintech, hospitality">
                            </div>

                            <div class="field">
                                <label for="recipient_name">Recipient Name</label>
                                <input id="recipient_name" type="text" name="recipient_name"
                                    value="{{ old('recipient_name') }}" placeholder="Nora B. Kelvin">
                            </div>

                            <div class="field">
                                <label for="recipient_title">Recipient Title</label>
                                <input id="recipient_title" type="text" name="recipient_title"
                                    value="{{ old('recipient_title') }}" placeholder="Managing Director">
                            </div>

                            <div class="field">
                                <label for="recipient_email">Recipient Email</label>
                                <input id="recipient_email" type="email" name="recipient_email"
                                    value="{{ old('recipient_email') }}" placeholder="nora@example.com">
                            </div>

                            <div class="field">
                                <label for="recipient_phone">Recipient Phone</label>
                                <input id="recipient_phone" type="text" name="recipient_phone"
                                    value="{{ old('recipient_phone') }}" placeholder="+1 555 010 3344">
                            </div>

                            <div class="field">
                                <label for="timeline">Timeline</label>
                                <input id="timeline" type="text" name="timeline"
                                    value="{{ old('timeline', '4 to 6 weeks') }}" placeholder="4 to 6 weeks" required>
                            </div>

                            <div class="field">
                                <label for="valid_until">Invoice Valid Until</label>
                                <input id="valid_until" type="date" name="valid_until"
                                    value="{{ old('valid_until', now()->addDays(14)->toDateString()) }}" required>
                            </div>

                            <div class="wizard-note">
                                <strong>Confirm the recipient</strong>
                                <p>Keep client and validity details ready to send.</p>
                            </div>
                        </div>

                        <div class="wizard-actions">
                            <span class="admin-pill">Step 2 of 4</span>
                            <div class="wizard-actions-group">
                                <button type="button" class="ghost-button" data-wizard-prev>Back</button>
                                <button type="button" class="button" data-wizard-next>Continue</button>
                            </div>
                        </div>
                    </section>

                    <section class="wizard-pane {{ $initialWizardStep === 2 ? 'is-active' : '' }}" data-wizard-pane
                        data-step-index="2" id="wizard-panel-delivery" role="tabpanel"
                        aria-labelledby="wizard-tab-delivery">
                        <div class="wizard-pane-grid">
                            @include('admin.quotes.partials.line-items-editor', [
                                'lineItems' => $lineItemsDefault,
                                'priceBounds' => $priceBounds,
                                'exchangeRate' => $exchangeRateDefault,
                            ])

                            <div class="field-full">
                                <label for="outcomes">Expected Outcomes</label>
                                <textarea id="outcomes" name="outcomes" data-rich-editor>{{ $outcomesDefault }}</textarea>
                                <p class="field-hint">One outcome per line.</p>
                            </div>

                            <div class="field-full">
                                <label for="milestones">Delivery Milestones</label>
                                <textarea id="milestones" name="milestones" data-rich-editor>{{ $milestonesDefault }}</textarea>
                                <p class="field-hint">Use one line per milestone.</p>
                            </div>

                            <div class="field-full">
                                <label for="optional_addons">Optional Add-ons</label>
                                <textarea id="optional_addons" name="optional_addons" data-rich-editor>{{ $addonsDefault }}</textarea>
                                <p class="field-hint">Optional extensions for the invoice.</p>
                            </div>
                        </div>

                        <div class="wizard-actions">
                            <span class="admin-pill">Step 3 of 4</span>
                            <div class="wizard-actions-group">
                                <button type="button" class="ghost-button" data-wizard-prev>Back</button>
                                <button type="button" class="button" data-wizard-next>Continue</button>
                            </div>
                        </div>
                    </section>

                    <section class="wizard-pane {{ $initialWizardStep === 3 ? 'is-active' : '' }}" data-wizard-pane
                        data-step-index="3" id="wizard-panel-finish" role="tabpanel"
                        aria-labelledby="wizard-tab-finish">
                        <div class="wizard-pane-grid">
                            <div class="field-full">
                                <label for="intro_message">Opening Message</label>
                                <textarea id="intro_message" name="intro_message" data-rich-editor>{{ $introDefault }}</textarea>
                            </div>

                            <div class="field-full">
                                <label for="closing_note">Closing Note</label>
                                <textarea id="closing_note" name="closing_note" data-rich-editor>{{ $closingDefault }}</textarea>
                            </div>

                            <div class="field-full">
                                <div class="review-grid">
                                    <article class="review-card">
                                        <strong>Invoice snapshot</strong>
                                        <span>Template: <span data-review-field="template" data-review-fallback="Select a template">Select a template</span></span>
                                        <span>Category: <span data-review-field="project_category" data-review-fallback="Choose a category">Choose a category</span></span>
                                        <span>Company: <span data-review-field="company_name" data-review-fallback="Add company details">Add company details</span></span>
                                        <span>Project: <span data-review-field="project_title" data-review-fallback="Add project title">Add project title</span></span>
                                    </article>

                                    <article class="review-card">
                                        <strong>Commercial frame</strong>
                                        <span>Investment: <span data-review-field="investment_amount" data-review-fallback="Set investment">Set investment</span></span>
                                        <span>Naira total: <span data-review-field="naira_total" data-review-fallback="Set amount and rate">Set amount and rate</span></span>
                                        <span>Exchange rate: <span data-review-field="exchange_rate" data-review-fallback="Set rate">Set rate</span></span>
                                        <span>Timeline: <span data-review-field="timeline" data-review-fallback="Set timeline">Set timeline</span></span>
                                        <span>Valid until: <span data-review-field="valid_until" data-review-fallback="Set expiry date">Set expiry date</span></span>
                                        <span>Recipient: <span data-review-field="recipient_email" data-review-fallback="Optional">Optional</span></span>
                                    </article>
                                </div>
                            </div>

                            <div class="field-full">
                                <div class="review-card">
                                    <strong>Before you generate</strong>
                                    <ul class="review-list">
                                        <li>Confirm template, pricing, and timeline.</li>
                                        <li>Check scope, outcomes, and milestones.</li>
                                        <li>Generate when the PDF is ready to save.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-actions">
                            <span class="admin-pill">Step 4 of 4</span>
                            <div class="wizard-actions-group">
                                <button type="button" class="ghost-button" data-wizard-prev>Back</button>
                                <button type="submit" class="button">Generate Invoice</button>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <span class="eyebrow">Snapshot</span>
                <h3 class="panel-title">Before build</h3>

                <div class="meta-list" style="margin-top: 18px;">
                    @foreach ($sidebarStats as $stat)
                        <div class="meta-item">
                            <span>{{ $stat['label'] }}</span>
                            <strong>{{ $stat['value'] }}</strong>
                            <p>{{ $stat['meta'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Recent Invoices</span>
                <h3 class="panel-title">Latest activity</h3>

                <ul class="mini-list" style="margin-top: 18px;">
                    @forelse ($quotes->take(5) as $quote)
                        <li>
                            <strong>{{ $quote->company_name }}</strong>
                            <span>{{ $quote->quote_number }} / ${{ number_format((float) $quote->investment_amount, 0) }}</span>
                        </li>
                    @empty
                        <li>
                            <strong>No saved invoices yet</strong>
                            <span>Generated invoices appear here.</span>
                        </li>
                    @endforelse
                </ul>
            </section>

            <section class="panel panel-padded">
                <span class="eyebrow">Recent Leads</span>
                <h3 class="panel-title">Contact activity</h3>

                @if ($recentMessages->isNotEmpty())
                    <div class="activity-feed" style="margin-top: 18px;">
                        @foreach ($recentMessages as $message)
                            <div class="activity-item">
                                <div class="activity-item-header">
                                    <div>
                                        <strong>{{ $message->name }}</strong>
                                        <span>{{ $message->topic }}</span>
                                    </div>
                                    <span>{{ optional($message->created_at)->format('M d') }}</span>
                                </div>
                                <p>{{ \Illuminate\Support\Str::limit($message->message, 110) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="data-note" style="margin-top: 18px;">
                        Contact enquiries will appear here.
                    </div>
                @endif
            </section>
        </aside>
    </div>
    @endif

    @if ($showArchive)
    <div class="section-heading">
        <div>
            <span class="eyebrow">Invoice Archive</span>
            <h2>Saved invoices</h2>
            <p>Preview, edit, export, or generate an MOU from one menu.</p>
        </div>
        <span class="admin-pill">{{ $quoteCount }} saved invoices</span>
    </div>

    <section class="panel panel-padded" id="saved-quotes">
        <div class="panel-head">
            <span class="eyebrow">Archive</span>
            <h2>Invoice list</h2>
            <p>Recent documents and exports.</p>
        </div>

        <div class="table-wrap">
            <table class="quote-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Company</th>
                        <th>Template</th>
                        <th>Investment</th>
                        <th>Validity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotes as $quote)
                        <tr>
                            <td>
                                <strong>{{ $quote->quote_number }}</strong>
                                <span>{{ $quote->project_category }}</span>
                            </td>
                            <td>
                                <strong>{{ $quote->company_name }}</strong>
                                <span>{{ $quote->project_title }}</span>
                            </td>
                            <td>
                                <strong>{{ $templates[$quote->template]['name'] ?? ucfirst($quote->template) }}</strong>
                                <span>{{ $templates[$quote->template]['badge'] ?? 'Invoice' }}</span>
                            </td>
                            <td>
                                <strong>${{ number_format((float) $quote->investment_amount, 0) }}</strong>
                                <span>{{ $quote->timeline }}</span>
                            </td>
                            <td>
                                <strong>{{ optional($quote->valid_until)->format('M d, Y') }}</strong>
                                <span>Created {{ optional($quote->created_at)->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <details class="action-menu">
                                    <summary>Actions</summary>
                                    <div class="action-menu-panel">
                                        <a href="{{ route('admin.quotes.show', $quote) }}">Preview</a>
                                        <a href="{{ route('admin.quotes.edit', $quote) }}">Edit</a>
                                        <a href="{{ route('admin.quotes.pdf', $quote) }}">Download PDF</a>
                                        <a href="{{ route('admin.quotes.mou', $quote) }}">Download MOU</a>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <strong>No invoices created yet.</strong>
                                <span>Create the first invoice using the builder above.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    @endif
@endsection

@push('scripts')
    <script>
        (() => {
            const wizard = document.querySelector('[data-quote-wizard]');

            if (!wizard) {
                return;
            }

            const form = wizard.querySelector('form');
            const panes = Array.from(wizard.querySelectorAll('[data-wizard-pane]'));
            const stepButtons = Array.from(wizard.querySelectorAll('[data-wizard-step-button]'));
            const nextButtons = Array.from(wizard.querySelectorAll('[data-wizard-next]'));
            const prevButtons = Array.from(wizard.querySelectorAll('[data-wizard-prev]'));
            const reviewFields = Array.from(wizard.querySelectorAll('[data-review-field]'));
            let currentStep = Number(wizard.dataset.initialStep || 0);

            const getNamedControls = (name) => Array.from(form.querySelectorAll(`[name="${name}"]`));

            const getValue = (name) => {
                if (name === 'naira_total') {
                    const total = Number(getNamedControls('investment_amount')[0]?.value || 0);
                    const rate = Number(getNamedControls('exchange_rate')[0]?.value || 0);

                    return total && rate ? `NGN ${(total * rate).toLocaleString(undefined, {
                        maximumFractionDigits: 0,
                    })}` : '';
                }

                const controls = getNamedControls(name);

                if (controls.length === 0) {
                    return '';
                }

                if (controls[0].type === 'radio') {
                    const selected = controls.find((control) => control.checked);

                    if (!selected) {
                        return '';
                    }

                    if (name === 'template') {
                        return selected.closest('.template-card')?.querySelector('strong')?.textContent?.trim() || selected.value;
                    }

                    return selected.value;
                }

                const field = controls[0];

                if (field.tagName === 'SELECT') {
                    return field.options[field.selectedIndex]?.text?.trim() || '';
                }

                if (name === 'investment_amount' && field.value) {
                    return `$${Number(field.value).toLocaleString(undefined, {
                        maximumFractionDigits: 0,
                    })}`;
                }

                if (name === 'exchange_rate' && field.value) {
                    return `$1 = NGN ${Number(field.value).toLocaleString(undefined, {
                        maximumFractionDigits: 2,
                    })}`;
                }

                if (name === 'valid_until' && field.value) {
                    const date = new Date(`${field.value}T00:00:00`);

                    return Number.isNaN(date.getTime()) ? field.value : date.toLocaleDateString(undefined, {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                    });
                }

                return field.value.trim();
            };

            const updateReview = () => {
                reviewFields.forEach((node) => {
                    const value = getValue(node.dataset.reviewField);
                    node.textContent = value || node.dataset.reviewFallback || 'Not provided';
                });
            };

            const getInvalidField = (index) => {
                const pane = panes[index];

                if (!pane) {
                    return null;
                }

                const invalidEditor = window.validateRichEditorsIn?.(pane, false);

                if (invalidEditor) {
                    return invalidEditor;
                }

                return Array.from(pane.querySelectorAll('input, select, textarea'))
                    .filter((field) => !field.disabled && field.willValidate)
                    .find((field) => !field.checkValidity()) || null;
            };

            const validateStep = (index) => {
                const invalidField = getInvalidField(index);

                if (!invalidField) {
                    return true;
                }

                if (typeof invalidField.reportValidity === 'function') {
                    invalidField.reportValidity();
                } else {
                    invalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    invalidField.querySelector('[contenteditable]')?.focus();
                }
                return false;
            };

            const setStep = (index) => {
                currentStep = Math.max(0, Math.min(index, panes.length - 1));

                panes.forEach((pane, paneIndex) => {
                    const isActive = paneIndex === currentStep;
                    pane.classList.toggle('is-active', isActive);
                    pane.hidden = !isActive;
                });

                stepButtons.forEach((button, buttonIndex) => {
                    const isActive = buttonIndex === currentStep;
                    const isComplete = buttonIndex < currentStep;
                    button.classList.toggle('is-active', isActive);
                    button.classList.toggle('is-complete', isComplete);
                    button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
            };

            stepButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const target = Number(button.dataset.stepIndex);

                    if (target > currentStep && !validateStep(currentStep)) {
                        return;
                    }

                    setStep(target);
                });
            });

            nextButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    if (!validateStep(currentStep)) {
                        return;
                    }

                    setStep(currentStep + 1);
                });
            });

            prevButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    setStep(currentStep - 1);
                });
            });

            form.addEventListener('input', updateReview);
            form.addEventListener('change', updateReview);
            form.addEventListener('submit', (event) => {
                for (let index = 0; index < panes.length; index += 1) {
                    const invalidField = getInvalidField(index);

                    if (!invalidField) {
                        continue;
                    }

                    event.preventDefault();
                    setStep(index);
                    window.requestAnimationFrame(() => {
                        if (typeof invalidField.reportValidity === 'function') {
                            invalidField.reportValidity();
                        } else {
                            invalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            invalidField.querySelector('[contenteditable]')?.focus();
                        }
                    });
                    return;
                }
            });

            setStep(currentStep);
            updateReview();
        })();
    </script>
@endpush
