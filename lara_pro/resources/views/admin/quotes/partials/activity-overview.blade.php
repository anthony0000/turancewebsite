<section class="tt-page tt-page--activity" id="performance-overview">
    <header class="tt-subpage-head">
        <div><span class="eyebrow">Performance</span><h1>Activity at a glance</h1><p>Recent traffic, leads, and invoice output.</p></div>
        <span class="tt-page-badge"><i></i>Live view</span>
    </header>

    <div class="tt-metric-band" aria-label="Performance metrics">
        @foreach ($kpiCards as $card)
            <div class="tt-metric"><span class="metric-label">{{ $card['label'] }}</span><strong>{{ $card['value'] }}</strong><span class="tt-metric-trend tt-metric-trend--{{ $card['trend']['direction'] }}">{{ $card['trend']['label'] }}</span></div>
        @endforeach
    </div>

    <div class="tt-analytics-grid tt-activity-analytics">
        <section class="tt-section tt-performance">
            <div class="tt-section-head">
                <div><span class="eyebrow">Activity</span><h2>Performance trend</h2></div>
                <div class="tt-chart-tools"><span>Last {{ $dashboardPeriodDays ?? 14 }} days</span><nav class="dashboard-chart-periods" aria-label="Chart period">@foreach ($dashboardPeriods as $periodDays => $periodLabel)<a class="{{ $dashboardPeriodDays === $periodDays ? 'active' : '' }}" href="{{ route('admin.quotes.activity', ['period' => $periodDays]) }}">{{ $periodLabel }}</a>@endforeach</nav></div>
            </div>
            <div class="tt-chart-legend" aria-label="Chart legend"><span><i class="tt-legend-dot tt-legend-dot--visits"></i>Visits</span><span><i class="tt-legend-dot tt-legend-dot--quotes"></i>Invoices</span><span><i class="tt-legend-dot tt-legend-dot--messages"></i>Leads</span></div>
            @include('admin.quotes.partials.activity-chart', ['chartOnly' => true])
        </section>

        <section class="tt-section tt-activity-middle">
            <div class="tt-section-head"><div><span class="eyebrow">Signals</span><h2>Current movement</h2></div></div>
            <div class="tt-insight-list">
                @foreach (array_slice($dashboardHighlights, 0, 3) as $highlight)
                    <div class="tt-insight-item"><span>{{ $highlight['label'] }}</span><strong>{{ $highlight['value'] }}</strong><small>{{ $highlight['meta'] }}</small></div>
                @endforeach
            </div>
        </section>

        <aside class="tt-section tt-activity-rail">
            <div class="tt-section-head"><div><span class="eyebrow">Reach</span><h2>Top pages</h2></div><span class="tt-rail-period">30D</span></div>
            @if ($topPages !== [])
                <div class="tt-page-list">
                    @foreach ($topPages as $page)
                        <div class="tt-page-row"><div><strong>{{ $page['label'] }}</strong><span>{{ $page['meta'] }}</span></div><strong>{{ number_format($page['count']) }}</strong><div class="tt-page-track"><span style="width: {{ max(3, $page['width']) }}%"></span></div></div>
                    @endforeach
                </div>
            @else
                <div class="tt-empty tt-empty--small"><strong>No page visits yet.</strong><span>Traffic will appear as tracking collects data.</span></div>
            @endif
        </aside>
    </div>

    <div class="tt-detail-strip" aria-label="Activity summary">
        <div class="tt-detail-item"><span>{{ $dashboardPeriodDays ?? 14 }}-day visits</span><strong>{{ number_format($dailyOverview['totals']['visits']) }}</strong></div>
        <div class="tt-detail-item"><span>{{ $dashboardPeriodDays ?? 14 }}-day invoices</span><strong>{{ number_format($dailyOverview['totals']['quotes']) }}</strong></div>
        <div class="tt-detail-item"><span>{{ $dashboardPeriodDays ?? 14 }}-day leads</span><strong>{{ number_format($dailyOverview['totals']['messages']) }}</strong></div>
        <div class="tt-detail-item"><span>Peak traffic day</span><strong>{{ $dailyOverview['peak']['full_label'] ?? '—' }}</strong></div>
    </div>

    @unless ($visitTrackingReady)
        <div class="data-note">Visit analytics will populate after tracking is enabled.</div>
    @endunless
</section>
