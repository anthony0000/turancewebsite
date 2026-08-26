@php
    $insightPipelineProgress = $totalPipeline > 0
        ? max(8, min(96, (int) round(($currentPipeline / $totalPipeline) * 100)))
        : 0;
@endphp

<section class="tt-page tt-page--insights" id="business-insights">
    <header class="tt-subpage-head">
        <div><span class="eyebrow">Business insights</span><h1>Patterns behind the pipeline</h1><p>Template demand, category focus, and commercial movement.</p></div>
        <span class="tt-page-badge"><i class="tt-page-badge__gold"></i>Decision view</span>
    </header>

    <div class="tt-metric-band" aria-label="Insight metrics">
        <div class="tt-metric"><span class="metric-label">Pipeline value</span><strong>${{ number_format($totalPipeline, 0) }}</strong><span class="tt-metric-trend">All invoices</span></div>
        <div class="tt-metric"><span class="metric-label">Average invoice</span><strong>${{ number_format($averageQuoteValue, 0) }}</strong><span class="tt-metric-trend">Across saved work</span></div>
        <div class="tt-metric"><span class="metric-label">Invoice conversion</span><strong>{{ $dashboardHighlights[2]['value'] }}</strong><span class="tt-metric-trend">Last 30 days</span></div>
        <div class="tt-metric"><span class="metric-label">Lead capture</span><strong>{{ $dashboardHighlights[3]['value'] }}</strong><span class="tt-metric-trend">Last 30 days</span></div>
    </div>

    <div class="tt-insights-grid">
        <section class="tt-section">
            <div class="tt-section-head"><div><span class="eyebrow">Templates</span><h2>Most used styles</h2></div><a class="panel-head__link" href="{{ route('admin.quotes.create') }}">Create invoice</a></div>
            @if ($templateBreakdown !== [])
                <div class="tt-ranking-list">@foreach ($templateBreakdown as $template)<div class="tt-ranking-row"><div><strong>{{ $template['label'] }}</strong><span>{{ $template['meta'] }}</span></div><strong>{{ number_format($template['count']) }}</strong><div class="tt-ranking-track"><span style="width: {{ max(3, $template['width']) }}%"></span></div></div>@endforeach</div>
            @else
                <div class="tt-empty"><strong>No template activity yet.</strong><span>Template rankings appear after invoices are stored.</span></div>
            @endif
        </section>

        <section class="tt-section tt-insights-category">
            <div class="tt-section-head"><div><span class="eyebrow">Categories</span><h2>Demand mix</h2></div></div>
            @if ($categoryBreakdown !== [])
                <div class="tt-ranking-list">@foreach ($categoryBreakdown as $category)<div class="tt-ranking-row"><div><strong>{{ $category['label'] }}</strong><span>{{ $category['meta'] }}</span></div><strong>{{ number_format($category['count']) }}</strong><div class="tt-ranking-track tt-ranking-track--lead"><span style="width: {{ max(3, $category['width']) }}%"></span></div></div>@endforeach</div>
            @else
                <div class="tt-empty"><strong>No category activity yet.</strong><span>Category data appears after invoices are stored.</span></div>
            @endif
        </section>

        <section class="tt-section tt-insight-pipeline">
            <div class="tt-section-head"><div><span class="eyebrow">Pipeline</span><h2>Commercial value</h2></div></div>
            <div class="tt-pipeline-ring" style="--pipeline-progress: {{ $insightPipelineProgress }}%;"><div class="tt-pipeline-ring__inner"><strong>${{ number_format($totalPipeline, 0) }}</strong><span>All invoices</span></div></div>
            <div class="tt-pipeline-breakdown"><div><span>This month</span><strong>${{ number_format($currentPipeline, 0) }}</strong></div><div><span>Invoices</span><strong>{{ number_format($quoteCount) }}</strong></div><div><span>Leads</span><strong>{{ number_format($contactCount) }}</strong></div></div>
        </section>
    </div>

    <div class="tt-detail-strip"><div class="tt-detail-item"><span>Leading template</span><strong>{{ $dashboardHighlights[0]['value'] }}</strong></div><div class="tt-detail-item"><span>Strongest category</span><strong>{{ $dashboardHighlights[1]['value'] }}</strong></div><div class="tt-detail-item"><span>Peak traffic day</span><strong>{{ $dailyOverview['peak']['full_label'] ?? '—' }}</strong></div><div class="tt-detail-item"><span>Visit tracking</span><strong>{{ $visitTrackingReady ? 'Live' : 'Pending' }}</strong></div></div>
</section>
