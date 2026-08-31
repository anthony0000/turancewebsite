@extends('admin.layouts.app')

@section('title', match (request()->route()?->getName()) {
    'admin.quotes.activity' => 'Activity | Admin',
    'admin.quotes.insights' => 'Insights | Admin',
    'admin.quotes.promotion' => 'Promotion Control | Admin',
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
        $addonsDefault = old('optional_addons', '');
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
                'label' => 'Brief',
                'description' => 'Choose direction',
                'fields' => ['template', 'project_category', 'project_title', 'executive_summary'],
            ],
            [
                'id' => 'client',
                'label' => 'Client',
                'description' => 'Add client',
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
                'description' => 'Price scope',
                'fields' => ['line_items', 'outcomes', 'milestones', 'optional_addons'],
            ],
            [
                'id' => 'finish',
                'label' => 'Review',
                'description' => 'Check and send',
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
        $showPromotion = $adminSection === 'promotion';
        $showBuilder = $adminSection === 'builder';
        $showArchive = $adminSection === 'archive';
        $dashboardPeriodDays = $dashboardPeriodDays ?? 14;
        $dashboardPeriods = [7 => '7D', 14 => '14D', 30 => '30D', 90 => '3M', 180 => '6M', 365 => '1Y'];
        $anniversaryPromo = $anniversaryPromo ?? config('seo.anniversary_promo', []);
        $promoApplyDefault = old('discount_percent', $anniversaryPromo['is_active'] ?? false ? ($anniversaryPromo['discount_percent'] ?? 0) : 0);
    @endphp

    @if ($showOverview)
        @include('admin.quotes.partials.overview')
    @endif

    @if (false && $showOverview)
    <section class="dash-topline" id="dashboard-overview" aria-label="Workspace status">
        <div class="status-chip-row">
            <span class="status-chip status-chip--{{ $visitTrackingReady ? 'ok' : 'warn' }}">
                <i aria-hidden="true"></i>
                Tracking {{ $visitTrackingReady ? 'live' : 'pending setup' }}
            </span>

            @if (! empty($anniversaryPromo['is_active']))
                <span class="status-chip status-chip--promo">
                    <i aria-hidden="true"></i>
                    {{ $anniversaryPromo['discount_percent'] }}% offer live
                    <small>{{ $anniversaryPromo['code'] }} · ends {{ $anniversaryPromo['ends_at_formatted'] }}</small>
                </span>
            @endif

            <span class="status-chip">
                <i aria-hidden="true"></i>
                {{ number_format($visitsThisMonth) }} visits this month
            </span>
        </div>

        <div class="dash-topline__actions">
            @if ($quotes->isNotEmpty())
                <a class="ghost-button" href="{{ route('admin.quotes.show', $quotes->first()) }}">Open latest</a>
            @endif
            <a class="ghost-button" href="{{ route('admin.quotes.activity') }}">View activity</a>
        </div>
    </section>

    @include('admin.quotes.partials.kpi-cards', ['compactKpi' => true])

    <div class="dashboard-grid dashboard-overview-grid">
        <section class="panel panel-padded dashboard-overview-chart-panel">
            <div class="panel-head panel-head--row dashboard-chart-head">
                <div>
                    <span class="eyebrow">Performance</span>
                    <h2>Activity trend</h2>
                </div>

                <div class="dashboard-chart-head-tools">
                    <nav class="dashboard-chart-periods" aria-label="Chart period">
                        @foreach ($dashboardPeriods as $periodDays => $periodLabel)
                            <a class="{{ $dashboardPeriodDays === $periodDays ? 'active' : '' }}" href="{{ route($showActivity ? 'admin.quotes.activity' : 'admin.quotes.index', ['period' => $periodDays]) }}">{{ $periodLabel }}</a>
                        @endforeach
                    </nav>
                    <div class="chart-legend" aria-label="Chart legend">
                        <span class="legend-item"><span class="legend-swatch legend-swatch--visits"></span>Visits</span>
                        <span class="legend-item"><span class="legend-swatch legend-swatch--quotes"></span>Invoices</span>
                        <span class="legend-item"><span class="legend-swatch legend-swatch--messages"></span>Leads</span>
                    </div>
                </div>
            </div>

            @include('admin.quotes.partials.activity-chart', ['chartSummaryCompact' => true])

            <div class="dashboard-signal-strip" aria-label="Business signals">
                @foreach ($dashboardHighlights as $highlight)
                    <div class="dashboard-signal" title="{{ $highlight['meta'] }}">
                        <span class="metric-label">{{ $highlight['label'] }}</span>
                        <strong>{{ $highlight['value'] }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <div class="panel-head panel-head--tight">
                    <span class="eyebrow">Pipeline</span>
                    <h3 class="panel-title">Monthly value</h3>
                </div>

                @if ($monthlyPipeline !== [])
                    <div class="mini-chart mini-chart--dashboard">
                        @foreach ($monthlyPipeline as $month)
                            <div class="month-bar">
                                <span>{{ $month['formatted_total'] }}</span>
                                <div class="month-bar-column" style="height: {{ max(6, $month['height'] * 1.7) }}px;"></div>
                                <strong>{{ $month['label'] }}</strong>
                                <span>{{ number_format($month['count']) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="data-note">Pipeline data will appear after the first invoice.</div>
                @endif
            </section>

            <section class="panel panel-padded">
                <div class="panel-head panel-head--tight">
                    <span class="eyebrow">Shortcuts</span>
                    <h3 class="panel-title">Workspaces</h3>
                </div>

                <div class="shortcut-grid">
                    <a href="{{ route('admin.quotes.activity') }}">
                        <span class="shortcut-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 17l5-5 4 4 7-8" /><path d="M4 19h16" /></svg></span>
                        <strong>Activity</strong>
                    </a>
                    <a href="{{ route('admin.quotes.insights') }}">
                        <span class="shortcut-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3a7 7 0 0 0-4 12.75V18h8v-2.25A7 7 0 0 0 12 3z" /><path d="M9 21h6" /></svg></span>
                        <strong>Insights</strong>
                    </a>
                    <a href="{{ route('admin.quotes.create') }}">
                        <span class="shortcut-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h10v18l-2-1-2 1-2-1-2 1-2-1z" /><path d="M9 8h6" /><path d="M9 12h6" /></svg></span>
                        <strong>Builder</strong>
                    </a>
                    <a href="{{ route('admin.quotes.archive') }}">
                        <span class="shortcut-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16v13H4z" /><path d="M4 7l2-4h12l2 4" /><path d="M9 12h6" /></svg></span>
                        <strong>Archive</strong>
                    </a>
                </div>
            </section>

            <section class="panel panel-padded">
                <div class="panel-head panel-head--tight panel-head--row">
                    <div>
                        <span class="eyebrow">Latest invoices</span>
                        <h3 class="panel-title">Recent output</h3>
                    </div>
                    @if ($quotes->isNotEmpty())
                        <a class="panel-head__link" href="{{ route('admin.quotes.archive') }}">View all</a>
                    @endif
                </div>

                <ul class="record-list">
                    @forelse ($quotes->take(4) as $quote)
                        <li>
                            <a href="{{ route('admin.quotes.show', $quote) }}">
                                <span class="record-list__main">
                                    <strong>{{ $quote->company_name }}</strong>
                                    <span>{{ $quote->quote_number }}</span>
                                </span>
                                <span class="record-list__amount">${{ number_format((float) $quote->investment_amount, 0) }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="record-list__empty">
                            <strong>No invoices yet</strong>
                            <span>Create one from the builder.</span>
                        </li>
                    @endforelse
                </ul>
            </section>
        </aside>
    </div>
    @endif

    @if ($showActivity)
        @include('admin.quotes.partials.activity-overview')
    @endif

    @if (false && $showActivity)
    <div class="section-heading" id="performance-overview">
        <div>
            <span class="eyebrow">Performance</span>
            <h2>Activity at a glance</h2>
            <p>Recent traffic, leads, and invoice output.</p>
        </div>
        <span class="admin-pill">Live view</span>
    </div>

    @include('admin.quotes.partials.kpi-cards')

    <div class="analytics-grid">
        <section class="panel panel-padded">
            <div class="panel-head panel-head--row">
                <div>
                    <span class="eyebrow">Activity</span>
                    <h2>{{ $dashboardPeriodDays ?? 14 }}-day snapshot</h2>
                    <p>Visits, leads, and invoices.</p>
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
            </div>

            @php($chart = $dailyOverview['chart'])

            <div class="line-chart-shell">
                <svg class="line-chart" viewBox="0 0 {{ $chart['width'] }} {{ $chart['height'] + 24 }}"
                    role="img" aria-label="Daily visits, invoices and leads over the last {{ $dashboardPeriodDays ?? 14 }} days">
                    <defs>
                        <linearGradient id="chart-fill-visits" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#111111" stop-opacity="0.14" />
                            <stop offset="100%" stop-color="#111111" stop-opacity="0" />
                        </linearGradient>
                    </defs>

                    @foreach ($chart['ticks'] as $tick)
                        <line class="chart-grid-line" x1="{{ $chart['plot_left'] }}" y1="{{ $tick['y'] }}"
                            x2="{{ $chart['plot_right'] }}" y2="{{ $tick['y'] }}" />
                        <text class="chart-axis-label" x="{{ $chart['plot_left'] - 8 }}"
                            y="{{ $tick['y'] + 3.5 }}" text-anchor="end">{{ number_format($tick['value']) }}</text>
                    @endforeach

                    <g class="chart-plot">
                        <path class="chart-area" d="{{ $chart['series']['visits']['area'] }}"
                            fill="url(#chart-fill-visits)" />

                        <polyline class="chart-line chart-line--visits" points="{{ $chart['series']['visits']['line'] }}" />
                        <polyline class="chart-line chart-line--quotes" points="{{ $chart['series']['quotes']['line'] }}" />
                        <polyline class="chart-line chart-line--messages" points="{{ $chart['series']['messages']['line'] }}" />

                        @foreach (['visits', 'quotes', 'messages'] as $seriesKey)
                            @foreach ($chart['series'][$seriesKey]['points'] as $point)
                                @if ($point['value'] > 0)
                                    <circle class="chart-dot chart-dot--{{ $seriesKey }}"
                                        cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3.2">
                                        <title>{{ $point['label'] }} · {{ number_format($point['value']) }} {{ $seriesKey }}</title>
                                    </circle>
                                @endif
                            @endforeach
                        @endforeach
                    </g>

                    @foreach ($chart['x_labels'] as $label)
                        <text class="chart-axis-label" x="{{ $label['x'] }}" y="{{ $chart['height'] + 14 }}"
                            text-anchor="middle">{{ $label['full_label'] }}</text>
                    @endforeach
                </svg>

                @unless ($chart['has_data'])
                    <p class="chart-empty">No activity recorded in this window yet.</p>
                @endunless
            </div>

            <div class="chart-summary-grid">
                <div class="mini-card">
                        <span class="metric-label">{{ $dashboardPeriodDays ?? 14 }}-Day Visits</span>
                    <strong>{{ number_format($dailyOverview['totals']['visits']) }}</strong>
                    <p>Tracked page views.</p>
                </div>
                <div class="mini-card">
                        <span class="metric-label">{{ $dashboardPeriodDays ?? 14 }}-Day Invoices</span>
                    <strong>{{ number_format($dailyOverview['totals']['quotes']) }}</strong>
                    <p>Generated invoices.</p>
                </div>
                <div class="mini-card">
                        <span class="metric-label">{{ $dashboardPeriodDays ?? 14 }}-Day Leads</span>
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
                <div class="data-note">
                    Visit analytics will populate after the migration runs and new page views arrive.
                </div>
            @endif
        </section>

        <aside class="sticky-stack">
            <section class="panel panel-padded">
                <div class="panel-head panel-head--tight">
                    <span class="eyebrow">Signals</span>
                    <h3 class="panel-title">Current movement</h3>
                </div>

                <div class="stat-list">
                    @foreach ($dashboardHighlights as $highlight)
                        <div class="stat-row">
                            <span class="stat-row__label">{{ $highlight['label'] }}</span>
                            <strong class="stat-row__value">{{ $highlight['value'] }}</strong>
                            <span class="stat-row__meta">{{ $highlight['meta'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel panel-padded">
                <div class="panel-head panel-head--tight">
                    <span class="eyebrow">Top pages</span>
                    <h3 class="panel-title">Last 30 days</h3>
                </div>

                @if ($topPages !== [])
                    <div class="stat-list">
                        @foreach ($topPages as $page)
                            <div class="bar-row">
                                <div class="bar-header">
                                    <div>
                                        <strong>{{ $page['label'] }}</strong>
                                        <span class="bar-meta">{{ $page['meta'] }}</span>
                                    </div>
                                    <strong class="bar-count">{{ number_format($page['count']) }}</strong>
                                </div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ max(3, $page['width']) }}%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="data-note">No page-visit records yet.</div>
                @endif
            </section>
        </aside>
    </div>
    @endif

    @if ($showPromotion)
        @include('admin.quotes.partials.promotion-overview')
    @endif

    @if (false && $showPromotion)
    <div class="section-heading" id="promotion-control">
        <div>
            <span class="eyebrow">Landing Page</span>
            <h2>Promotion control</h2>
            <p>Manage the anniversary offer shown on the public landing page and used during invoice creation.</p>
        </div>
        <span class="admin-pill">Central control</span>
    </div>

    <div class="dashboard-grid promotion-control-grid">
        <section class="panel panel-padded">
            <div class="panel-head">
                <span class="eyebrow">Anniversary campaign</span>
                <h2>Offer settings</h2>
                <p>Changes here update the landing-page message, prices, countdown, and default invoice discount.</p>
            </div>
            <form method="POST" action="{{ route('admin.quotes.promotion.update') }}" class="promotion-form">
                @csrf
                <div class="field-full promotion-form__status">
                    <label class="admin-discount-toggle">
                        <input type="checkbox" name="enabled" value="1" @checked($anniversaryPromo['enabled'] ?? false)>
                        <span>Show this offer publicly</span>
                    </label>
                    <p class="field-hint">When disabled, the landing page hides the offer and countdown.</p>
                </div>
                <div class="promotion-form__grid">
                    <div class="field"><label for="promotion_years">Years celebrated</label><input id="promotion_years" type="number" name="years" value="{{ old('years', $anniversaryPromo['years'] ?? 7) }}" min="1" max="100" required></div>
                    <div class="field"><label for="promotion_discount">Discount percentage</label><input id="promotion_discount" type="number" name="discount_percent" value="{{ old('discount_percent', $anniversaryPromo['discount_percent'] ?? 50) }}" min="0" max="100" step="0.01" required></div>
                    <div class="field"><label for="promotion_code">Promo code</label><input id="promotion_code" type="text" name="promo_code" value="{{ old('promo_code', $anniversaryPromo['code'] ?? 'TURANCE7') }}" maxlength="80" required></div>
                    <div class="field"><label for="promotion_ends_at">Offer ends</label><input id="promotion_ends_at" type="datetime-local" name="ends_at" value="{{ old('ends_at', $anniversaryPromo['ends_at_input'] ?? '') }}" required></div>
                </div>
                <div class="wizard-actions"><span class="admin-pill">Current: {{ ($anniversaryPromo['is_active'] ?? false) ? 'Live' : 'Inactive' }}</span><button type="submit" class="button">Save promotion</button></div>
            </form>
        </section>
        <aside class="sticky-stack">
            <section class="panel panel-padded promotion-preview-card">
                <span class="eyebrow">Public preview</span>
                <strong class="promotion-preview-card__discount">{{ $anniversaryPromo['discount_percent'] ?? 50 }}% off</strong>
                <h3>{{ $anniversaryPromo['years'] ?? 7 }} years of Turance</h3>
                <p>Landing-page offer code <b>{{ $anniversaryPromo['code'] ?? 'TURANCE7' }}</b>.</p>
                <span class="data-note">Ends {{ $anniversaryPromo['ends_at_formatted'] ?? 'Not set' }}</span>
            </section>
        </aside>
    </div>
    @endif

    @if ($showInsights)
        @include('admin.quotes.partials.insights-overview')
    @endif

    @if (false && $showInsights)
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
            <div class="panel-head panel-head--tight">
                <span class="eyebrow">Templates</span>
                <h3 class="panel-title">Most used styles</h3>
            </div>

            @if ($templateBreakdown !== [])
                <div class="stat-list">
                    @foreach ($templateBreakdown as $template)
                        <div class="bar-row">
                            <div class="bar-header">
                                <div>
                                    <strong>{{ $template['label'] }}</strong>
                                    <span class="bar-meta">{{ $template['meta'] }}</span>
                                </div>
                                <strong class="bar-count">{{ number_format($template['count']) }}</strong>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill bar-fill--quote"
                                    style="width: {{ max(3, $template['width']) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-note">Template rankings appear after invoices are stored.</div>
            @endif
        </section>

        <section class="panel panel-padded">
            <div class="panel-head panel-head--tight">
                <span class="eyebrow">Categories</span>
                <h3 class="panel-title">Demand mix</h3>
            </div>

            @if ($categoryBreakdown !== [])
                <div class="stat-list">
                    @foreach ($categoryBreakdown as $category)
                        <div class="bar-row">
                            <div class="bar-header">
                                <div>
                                    <strong>{{ $category['label'] }}</strong>
                                    <span class="bar-meta">{{ $category['meta'] }}</span>
                                </div>
                                <strong class="bar-count">{{ number_format($category['count']) }}</strong>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill bar-fill--lead"
                                    style="width: {{ max(3, $category['width']) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-note">Category data appears after invoices are stored.</div>
            @endif
        </section>

        <section class="panel panel-padded">
            <div class="panel-head panel-head--tight">
                <span class="eyebrow">Pipeline</span>
                <h3 class="panel-title">Monthly value</h3>
            </div>

            @if ($monthlyPipeline !== [])
                <div class="mini-chart">
                    @foreach ($monthlyPipeline as $month)
                        <div class="month-bar">
                            <span>{{ $month['formatted_total'] }}</span>
                            <div class="month-bar-column" style="height: {{ max(6, $month['height'] * 1.7) }}px;"></div>
                            <strong>{{ $month['label'] }}</strong>
                            <span>{{ number_format($month['count']) }}
                                {{ \Illuminate\Support\Str::plural('invoice', $month['count']) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-note">Pipeline bars appear after invoice values accumulate.</div>
            @endif
        </section>
    </div>
    @endif

    @if ($showBuilder)
    <div class="section-heading tt-builder-head" id="invoice-studio">
        <div>
            <span class="eyebrow">Invoice Studio</span>
            <h2>Create the next invoice.</h2>
            <p>Brief <span aria-hidden="true">→</span> client <span aria-hidden="true">→</span> scope <span aria-hidden="true">→</span> send.</p>
        </div>
        <span class="tt-page-badge"><i class="tt-page-badge__gold"></i>4-step workflow</span>
    </div>

    <div class="dashboard-grid tt-builder-layout">
        <section class="panel panel-padded tt-section tt-builder-main" id="quote-builder">
            <div class="panel-head">
                <span class="eyebrow">New Invoice</span>
                <h2>Create invoice</h2>
                <p>Four focused steps from brief to send.</p>
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
                                <p class="field-hint">Optional extensions for the invoice and MOU. Leave blank if none apply.</p>
                            </div>

                            @include('admin.quotes.partials.discount-control', [
                                'anniversaryPromo' => $anniversaryPromo,
                                'discountPercent' => $promoApplyDefault,
                                'promoCode' => old('promo_code', $anniversaryPromo['code'] ?? ''),
                            ])
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
                                        <span>Discount: <span data-review-field="discount_percent" data-review-fallback="None">None</span></span>
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

        <aside class="sticky-stack tt-builder-rail">
            <section class="panel panel-padded tt-section">
                <div class="panel-head panel-head--tight">
                    <span class="eyebrow">Snapshot</span>
                    <h3 class="panel-title">Build context</h3>
                </div>

                <div class="stat-list">
                    @foreach ($sidebarStats as $stat)
                        <div class="stat-row">
                            <span class="stat-row__label">{{ $stat['label'] }}</span>
                            <strong class="stat-row__value">{{ $stat['value'] }}</strong>
                            <span class="stat-row__meta">{{ $stat['meta'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel panel-padded tt-section">
                <div class="panel-head panel-head--tight">
                    <span class="eyebrow">Recent invoices</span>
                    <h3 class="panel-title">Latest invoices</h3>
                </div>

                <ul class="record-list">
                    @forelse ($quotes->take(5) as $quote)
                        <li>
                            <a href="{{ route('admin.quotes.show', $quote) }}">
                                <span class="record-list__main">
                                    <strong>{{ $quote->company_name }}</strong>
                                    <span>{{ $quote->quote_number }}</span>
                                </span>
                                <span class="record-list__amount">${{ number_format((float) $quote->investment_amount, 0) }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="record-list__empty">
                            <strong>No saved invoices yet</strong>
                            <span>Generated invoices appear here.</span>
                        </li>
                    @endforelse
                </ul>
            </section>

            <section class="panel panel-padded tt-section">
                <div class="panel-head panel-head--tight">
                    <span class="eyebrow">Recent leads</span>
                    <h3 class="panel-title">Latest leads</h3>
                </div>

                @if ($recentMessages->isNotEmpty())
                    <div class="activity-feed">
                        @foreach ($recentMessages as $message)
                            <div class="activity-item">
                                <div class="activity-item-header">
                                    <div>
                                        <strong>{{ $message->name }}</strong>
                                        <span>{{ $message->topic }}{{ $message->promo_code ? ' · '.$message->promo_code : '' }}</span>
                                    </div>
                                    <span>{{ optional($message->created_at)->format('M d') }}</span>
                                </div>
                                <p>{{ \Illuminate\Support\Str::limit($message->message, 110) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="data-note">Contact enquiries will appear here.</div>
                @endif
            </section>
        </aside>
    </div>
    @endif

    @if ($showArchive)
        @include('admin.quotes.partials.archive-overview')
    @endif

    @if (false && $showArchive)
    <div class="section-heading">
        <div>
            <span class="eyebrow">Invoice Archive</span>
            <h2>Saved invoices</h2>
            <p>Preview, edit, export, or generate an MOU from one menu.</p>
        </div>
        <span class="admin-pill">{{ number_format($quoteCount) }}
            {{ \Illuminate\Support\Str::plural('invoice', $quoteCount) }} saved</span>
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

                if (name === 'discount_percent') {
                    const enabled = getNamedControls('discount_enabled')[0];
                    const value = getNamedControls('discount_percent')[0]?.value;

                    return enabled?.checked && value ? `${Number(value).toLocaleString(undefined, { maximumFractionDigits: 2 })}%` : 'None';
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
