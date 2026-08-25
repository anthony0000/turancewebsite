@php
    $chart = $dailyOverview['chart'];
    $chartSummaryCompact = $chartSummaryCompact ?? false;
@endphp

<div class="line-chart-shell">
    <svg class="line-chart" viewBox="0 0 {{ $chart['width'] }} {{ $chart['height'] + 24 }}"
        role="img" aria-label="Daily visits, invoices and leads over the last 14 days">
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
            <path class="chart-area" d="{{ $chart['series']['visits']['area'] }}" fill="url(#chart-fill-visits)" />
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

<div class="chart-summary-grid {{ $chartSummaryCompact ? 'chart-summary-grid--compact' : '' }}">
    <div class="mini-card">
        <span class="metric-label">{{ $chartSummaryCompact ? 'Visits' : '14-Day Visits' }}</span>
        <strong>{{ number_format($dailyOverview['totals']['visits']) }}</strong>
        @unless ($chartSummaryCompact)
            <p>Tracked page views.</p>
        @endunless
    </div>
    <div class="mini-card">
        <span class="metric-label">{{ $chartSummaryCompact ? 'Invoices' : '14-Day Invoices' }}</span>
        <strong>{{ number_format($dailyOverview['totals']['quotes']) }}</strong>
        @unless ($chartSummaryCompact)
            <p>Generated invoices.</p>
        @endunless
    </div>
    <div class="mini-card">
        <span class="metric-label">{{ $chartSummaryCompact ? 'Leads' : '14-Day Leads' }}</span>
        <strong>{{ number_format($dailyOverview['totals']['messages']) }}</strong>
        @unless ($chartSummaryCompact)
            <p>Contact enquiries.</p>
        @endunless
    </div>
    <div class="mini-card">
        <span class="metric-label">Peak day</span>
        <strong>{{ $dailyOverview['peak']['full_label'] ?? '—' }}</strong>
        @unless ($chartSummaryCompact)
            <p>
                {{ isset($dailyOverview['peak']['visits'])
                    ? number_format($dailyOverview['peak']['visits']).' visits'
                    : 'Waiting for traffic.' }}
            </p>
        @endunless
    </div>
</div>

@if (! $visitTrackingReady)
    <div class="data-note">Visit analytics will populate after tracking is enabled.</div>
@endif
