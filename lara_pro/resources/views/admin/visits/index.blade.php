@extends('admin.layouts.app')

@section('title', 'Website visits | Admin')

@push('styles')
    <style>@include('admin.visits.styles')</style>
@endpush

@section('content')
<div class="va-page">
    <header class="va-heading">
        <div><span class="eyebrow">Audience & acquisition</span><h1>Understand your website traffic.</h1><p>Explore how often your site is visited, which pages get attention, and where traffic comes from.</p></div>
        <div class="va-heading-actions">
            <span class="va-updated">Updated {{ $generatedAt->format('M j, H:i') }} · {{ config('app.timezone') }}</span>
            @if ($trackingReady)<a class="button" href="{{ route('admin.visits.export', $filters) }}">Export filtered visits <span aria-hidden="true">↓</span></a>@endif
        </div>
    </header>

    @if ($errors->any())
        <div class="alert alert-warning" role="alert"><div><strong>Review the filters.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
    @endif

    <section class="panel va-filters" aria-label="Filter website visits">
        <div class="va-filter-top"><strong>Explore a period</strong><nav class="va-presets" aria-label="Date presets">
            @foreach ([7 => '7 days', 30 => '30 days', 90 => '90 days', 365 => '1 year'] as $days => $label)
                @php
                    $active = $filters['start'] === today()->subDays($days - 1)->toDateString() && $filters['end'] === today()->toDateString();
                @endphp
                <a class="{{ $active ? 'active' : '' }}" @if ($active) aria-current="true" @endif href="{{ route('admin.visits.index', array_merge(\Illuminate\Support\Arr::except($filters, ['start', 'end']), ['period' => $days])) }}">{{ $label }}</a>
            @endforeach
        </nav></div>
        <form method="GET" action="{{ route('admin.visits.index') }}" class="va-filter-grid">
            <div class="field"><label for="va-start">From</label><input type="date" name="start" id="va-start" value="{{ old('start', $filters['start']) }}" max="{{ today()->toDateString() }}" required></div>
            <div class="field"><label for="va-end">Through</label><input type="date" name="end" id="va-end" value="{{ old('end', $filters['end']) }}" max="{{ today()->toDateString() }}" required></div>
            <div class="field"><label for="va-group">Page category</label><select name="page_group" id="va-group"><option value="">All categories</option>@foreach ($pageGroups as $group)<option value="{{ $group }}" @selected($filters['page_group'] === $group)>{{ $group }}</option>@endforeach</select></div>
            <div class="field"><label for="va-device">Device</label><select name="device" id="va-device"><option value="">All devices</option>@foreach (\App\Support\VisitAnalytics::DEVICES as $device)<option value="{{ $device }}" @selected($filters['device'] === $device)>{{ $device }}</option>@endforeach</select></div>
            <div class="field"><label for="va-traffic">Traffic</label><select name="traffic" id="va-traffic"><option value="all" @selected($filters['traffic'] === 'all')>All traffic</option><option value="people" @selected($filters['traffic'] === 'people')>Exclude detected bots</option><option value="bots" @selected($filters['traffic'] === 'bots')>Detected bots only</option></select></div>
            <div class="field va-path-filter"><label for="va-path">Exact page path</label><input type="text" name="path" id="va-path" value="{{ $filters['path'] }}" maxlength="255" placeholder="All pages, or e.g. /contact"></div>
            <div class="va-filter-actions"><button class="button" type="submit">Apply filters</button><a class="ghost-button" href="{{ route('admin.visits.index') }}">Reset</a></div>
        </form>
        <p class="va-filter-hint">Dates are inclusive, in {{ config('app.timezone') }}. All charts, tables, and exports follow these filters. Up to 366 days at a time.</p>
    </section>

    @if (! $trackingReady)
        <section class="panel va-empty"><span class="eyebrow">Tracking pending</span><h2>Visit analytics are not available yet.</h2><p>This page will populate when website visit tracking is available.</p></section>
    @else
        @php
            $summary = $report['summary'];
            $previous = $report['previous'];
            $total = $summary['views'];
            $metricCards = [
                ['key' => 'views', 'label' => 'Page views', 'hint' => 'Every recorded page request', 'decimals' => 0],
                ['key' => 'visitors', 'label' => 'Tracked browsers', 'hint' => 'Distinct recorded session IDs', 'decimals' => 0],
                ['key' => 'views_per_visitor', 'label' => 'Views / tracked browser', 'hint' => 'Only views with a session ID', 'decimals' => 2],
                ['key' => 'pages', 'label' => 'Pages visited', 'hint' => 'Distinct website paths', 'decimals' => 0],
            ];
            $comparisonLabel = $report['previousStart']->format('M j, Y').' – '.$report['previousEnd']->format('M j, Y');
        @endphp
        <div class="va-period-caption"><strong>{{ \Illuminate\Support\Carbon::parse($filters['start'])->format('M j, Y') }} – {{ \Illuminate\Support\Carbon::parse($filters['end'])->format('M j, Y') }}</strong><span>Compared with {{ $comparisonLabel }} · same filters</span></div>
        <section class="va-metrics" aria-label="Traffic summary">
            @foreach ($metricCards as $card)
                @php
                    $value = $summary[$card['key']];
                    $prior = $previous[$card['key']];
                    $change = $prior > 0 ? round(($value - $prior) / $prior * 100, 1) : null;
                @endphp
                <article class="panel va-metric"><span class="metric-label">{{ $card['label'] }}</span><strong>{{ number_format($value, $card['decimals']) }}</strong><span class="va-change {{ $change > 0 ? 'va-change--up' : ($change < 0 ? 'va-change--down' : '') }}">{{ $change !== null ? ($change > 0 ? '+' : '').number_format($change, 1).'%' : ($value > 0 ? 'No prior baseline' : 'No change') }} <small>previous: {{ number_format($prior, $card['decimals']) }}</small></span><p>{{ $card['hint'] }}</p></article>
            @endforeach
        </section>

        @if ($total === 0)
            <section class="panel va-empty"><h2>No visits match these filters.</h2><p>Try a wider date range or clear the device, traffic, and page filters.</p><a class="ghost-button" href="{{ route('admin.visits.index') }}">Show the last 30 days</a></section>
        @endif

        <div class="va-main-grid">
            <section class="panel va-card va-trend-card">
                <div class="va-section-head"><div><span class="eyebrow">Traffic over time</span><h2>Daily page views</h2><p>Hover over the chart, or open the daily data below.</p></div><button class="ghost-button" type="button" data-va-comparison aria-pressed="true">Previous period</button></div>
                @php
                    $trend = $report['trend'];
                    $chartMax = max(4, ceil(max(collect($trend)->max('views'), collect($trend)->max('previous')) / 4) * 4);
                    $point = fn ($index, $value) => (52 + ($index / max(1, count($trend) - 1)) * 896).','.round(218 - ($value / $chartMax) * 180, 2);
                    $points = collect($trend)->map(fn ($day, $index) => $point($index, $day['views']))->implode(' ');
                    $previousPoints = collect($trend)->map(fn ($day, $index) => $point($index, $day['previous']))->implode(' ');
                @endphp
                <div class="va-chart" data-va-chart>
                    <svg viewBox="0 0 1000 262" role="img" aria-labelledby="va-chart-title va-chart-desc">
                        <title id="va-chart-title">Daily page views compared with the previous period</title><desc id="va-chart-desc">{{ number_format($total) }} page views in the selected period. The complete daily values are available in the table below.</desc>
                        @for ($tick = 0; $tick <= 4; $tick++)
                            <line class="va-gridline" x1="52" x2="948" y1="{{ 218 - $tick * 45 }}" y2="{{ 218 - $tick * 45 }}"/>
                            <text class="va-axis" x="42" y="{{ 223 - $tick * 45 }}" text-anchor="end">{{ number_format($chartMax / 4 * $tick) }}</text>
                        @endfor
                        @if (count($trend) > 1)<polygon class="va-chart-fill" points="52,218 {{ $points }} 948,218"/>@endif
                        <g data-va-previous>
                            <polyline class="va-chart-previous" points="{{ $previousPoints }}"/>
                            @if (count($trend) === 1)<circle cx="52" cy="{{ 218 - $trend[0]['previous'] / $chartMax * 180 }}" r="4" fill="#9da8ba"/>@endif
                        </g>
                        <polyline class="va-chart-current" points="{{ $points }}"/>
                        @if (count($trend) === 1)<circle cx="52" cy="{{ 218 - $trend[0]['views'] / $chartMax * 180 }}" r="4" fill="#b98518"/>@endif
                        <line data-va-cursor x1="52" x2="52" y1="28" y2="218" class="va-chart-cursor" visibility="hidden"/>
                        <text class="va-axis" x="52" y="248">{{ $trend[0]['label'] }}</text><text class="va-axis" x="948" y="248" text-anchor="end">{{ $trend[count($trend) - 1]['label'] }}</text>
                    </svg>
                </div>
                <div class="va-chart-legend"><span><i></i>Selected period</span><span><i class="va-legend-previous"></i>Previous period</span><output data-va-readout aria-live="polite">{{ count($trend) }} days · {{ number_format($total / count($trend), 1) }} views per day</output></div>
                <details class="va-daily-data"><summary>View daily data</summary><div class="table-wrap"><table class="quote-table"><thead><tr><th scope="col">Date</th><th scope="col">Page views</th><th scope="col">Previous period, same day offset</th></tr></thead><tbody>@foreach ($trend as $day)<tr><th scope="row">{{ $day['date'] }}</th><td>{{ number_format($day['views']) }}</td><td>{{ number_format($day['previous']) }}</td></tr>@endforeach</tbody></table></div></details>
            </section>
            <aside class="panel va-card va-audience">
                <span class="eyebrow">Audience snapshot</span><h2>New & returning</h2><p>Tracked browsers seen in the selected views.</p>
                <div class="va-audience-numbers"><div><strong>{{ number_format($report['newVisitors']) }}</strong><span>First seen in this period</span></div><div><strong>{{ number_format($report['returning']) }}</strong><span>Seen before this period</span></div></div>
                <div class="va-audience-bar" aria-hidden="true"><span style="width: {{ $summary['visitors'] ? $report['newVisitors'] / $summary['visitors'] * 100 : 0 }}%"></span></div>
                <dl class="va-facts"><div><dt>Views without a session ID</dt><dd>{{ number_format($total - $summary['identified_views']) }}</dd></div><div><dt>Busiest date</dt><dd>{{ $total ? $report['peakDay']['date'] : '—' }}</dd></div><div><dt>Busiest hour</dt><dd>{{ $total ? $report['peakHour']['label'] : '—' }}</dd></div><div><dt>Recorded source groups</dt><dd>{{ number_format($report['sourceCount']) }}</dd></div></dl>
                <p class="va-note">A tracked browser is an estimate based on its session ID, not a unique person. Returning means that ID appears in earlier retained records.</p>
            </aside>
        </div>

        <div class="va-three-grid">
            @include('admin.visits.breakdown', ['eyebrow' => 'Acquisition', 'heading' => 'Referring websites', 'description' => 'Top 15 referrer hosts, including internal navigation. Not first-touch attribution.', 'rows' => $report['sources'], 'filterKey' => null])
            @include('admin.visits.breakdown', ['eyebrow' => 'Content', 'heading' => 'Page categories', 'description' => 'Select a category to explore its traffic.', 'rows' => $report['groups'], 'filterKey' => 'page_group'])
            @include('admin.visits.breakdown', ['eyebrow' => 'Technology', 'heading' => 'Devices', 'description' => 'Estimated from browser information. Select a device to filter.', 'rows' => $report['breakdowns']['device'], 'filterKey' => 'device'])
        </div>

        <section class="panel va-card" id="visited-pages">
            <div class="va-section-head"><div><span class="eyebrow">Content performance</span><h2>Pages people visit</h2><p>Ranked by recorded views. Select a path to inspect that page. Bot traffic follows the filter above.</p></div><span class="admin-pill">{{ number_format($report['pages']->total()) }} paths</span></div>
            <div class="table-wrap"><table class="quote-table va-pages-table"><thead><tr><th scope="col">Page path</th><th scope="col">Views</th><th scope="col">Share of views</th><th scope="col">Tracked browsers</th><th scope="col">Last recorded</th></tr></thead><tbody>
                @forelse ($report['pages'] as $page)
                    <tr><th scope="row"><a class="va-path" href="{{ route('admin.visits.index', array_merge($filters, ['path' => $page->path])) }}">{{ $page->path }}</a></th><td><strong>{{ number_format($page->views) }}</strong></td><td>{{ $total ? number_format($page->views / $total * 100, 1) : 0 }}%</td><td>{{ number_format($page->visitors) }}</td><td>{{ \Illuminate\Support\Carbon::parse($page->last_seen)->format('M j, H:i') }}</td></tr>
                @empty<tr><td colspan="5">No pages match this period.</td></tr>@endforelse
            </tbody></table></div>
            @include('admin.visits.pagination', ['paginator' => $report['pages'], 'label' => 'Popular pages', 'anchor' => 'visited-pages'])
        </section>

        <div class="va-two-grid">
            <section class="panel va-card"><div class="va-section-head"><div><span class="eyebrow">Timing</span><h2>When traffic arrives</h2><p>Total views by hour across the selected days · {{ config('app.timezone') }}.</p></div></div>
                @php
                    $hourMax = max(1, collect($report['hours'])->max('count'));
                @endphp
                <div class="va-hours" aria-label="Hourly page views">@foreach ($report['hours'] as $hour)<div class="va-hour"><span class="va-hour-value">{{ $hour['count'] }}</span><div><span style="height: {{ $hour['count'] / $hourMax * 100 }}%"></span></div><small>{{ substr($hour['label'], 0, 2) }}</small></div>@endforeach</div>
                <div class="va-weekdays">@foreach ([1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 0 => 'Sun'] as $index => $label)<div><span>{{ $label }}</span><strong>{{ number_format($report['weekdays'][$index]) }}</strong></div>@endforeach</div>
                <p class="va-note">Weekday figures are totals, not daily averages; longer ranges can include more of some weekdays.</p>
            </section>
            @include('admin.visits.breakdown', ['eyebrow' => 'Technology', 'heading' => 'Browsers', 'description' => 'Browser families inferred from the recorded user agent.', 'rows' => $report['breakdowns']['browser'], 'filterKey' => null])
        </div>

        <div class="va-two-grid">
            @include('admin.visits.breakdown', ['eyebrow' => 'Technology', 'heading' => 'Operating systems', 'description' => 'Device software inferred from browser information.', 'rows' => $report['breakdowns']['os'], 'filterKey' => null])
            <section class="panel va-card va-method"><span class="eyebrow">Reading the numbers</span><h2>What this report measures</h2><ul><li>Successful public HTML page requests recorded by the website. Repeat requests count again. Admin pages, redirects, and error responses are excluded.</li><li>Historical tracking includes HEAD requests. Cached pages that do not reach the application may not be recorded.</li><li>Bot detection and device classification are estimates. Some automated traffic may look like a regular browser.</li><li>“Direct / unavailable” means no referrer was recorded; it can include bookmarks, apps, or browsers that hide referrers.</li><li>Time on page, bounce rate, location, campaign tags, clicks, and individual conversion attribution are not currently measured.</li><li>Today may be incomplete. Comparisons use the immediately preceding range of the same length.</li></ul></section>
        </div>

        <section class="panel va-card" id="recent-visits">
            <div class="va-section-head"><div><span class="eyebrow">Visit log</span><h2>Recorded visits</h2><p>Newest first. Visitor labels are pseudonymous; raw session IDs and IP addresses are not shown.</p></div><a class="ghost-button" href="{{ route('admin.visits.export', $filters) }}">Download CSV</a></div>
            <div class="table-wrap"><table class="quote-table va-log-table"><thead><tr><th scope="col">Recorded at</th><th scope="col">Page / category</th><th scope="col">Visitor</th><th scope="col">Device</th><th scope="col">Browser / OS</th><th scope="col">Referrer host</th></tr></thead><tbody>
                @forelse ($report['recent'] as $visit)
                    <tr><td><time datetime="{{ $visit->created_at->toIso8601String() }}">{{ $visit->created_at->format('M j, Y') }}<small>{{ $visit->created_at->format('H:i:s') }}</small></time></td><td><a class="va-path" href="{{ route('admin.visits.index', array_merge($filters, ['path' => $visit->path])) }}">{{ $visit->path }}</a><small>{{ $visit->page_group ?: 'Uncategorised' }}</small></td><td>{{ $analytics->visitor($visit->session_id) }}</td><td><span class="va-device {{ $visit->device === 'Bot' ? 'va-device--bot' : '' }}">{{ $visit->device }}</span></td><td>{{ $visit->browser }}<small>{{ $visit->os }}</small></td><td class="va-source">{{ $analytics->source($visit->referrer) }}</td></tr>
                @empty<tr><td colspan="6">No recorded visits match these filters.</td></tr>@endforelse
            </tbody></table></div>
            @include('admin.visits.pagination', ['paginator' => $report['recent'], 'label' => 'Recorded visits', 'anchor' => 'recent-visits'])
        </section>
    @endif
</div>
@endsection

@push('scripts')
@if ($trackingReady)
<script>
    (() => {
        const days = @json($report['trend']);
        const chart = document.querySelector('[data-va-chart]');
        const readout = document.querySelector('[data-va-readout]');
        const cursor = document.querySelector('[data-va-cursor]');
        const comparison = document.querySelector('[data-va-previous]');
        const toggle = document.querySelector('[data-va-comparison]');
        let showPrevious = true;
        let activeIndex = -1;
        const renderReadout = () => {
            if (activeIndex < 0) return;
            const day = days[activeIndex];
            readout.textContent = `${day.date}: ${day.views.toLocaleString()} views${showPrevious ? ` · previous: ${day.previous.toLocaleString()}` : ''}`;
        };
        toggle?.addEventListener('click', () => {
            showPrevious = !showPrevious;
            toggle.setAttribute('aria-pressed', String(showPrevious));
            comparison.style.display = showPrevious ? '' : 'none';
            renderReadout();
        });
        chart?.addEventListener('pointermove', (event) => {
            const bounds = chart.querySelector('svg').getBoundingClientRect();
            const x = (event.clientX - bounds.left) / bounds.width * 1000;
            activeIndex = Math.max(0, Math.min(days.length - 1, Math.round((x - 52) / 896 * (days.length - 1))));
            const position = 52 + activeIndex / Math.max(1, days.length - 1) * 896;
            cursor.setAttribute('x1', position);
            cursor.setAttribute('x2', position);
            cursor.setAttribute('visibility', 'visible');
            renderReadout();
        });
        chart?.addEventListener('pointerleave', () => cursor.setAttribute('visibility', 'hidden'));
    })();
</script>
@endif
@endpush
