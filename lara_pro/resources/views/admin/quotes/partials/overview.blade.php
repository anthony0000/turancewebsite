@php
    $pipelineProgress = $totalPipeline > 0
        ? max(8, min(96, (int) round(($currentPipeline / $totalPipeline) * 100)))
        : 0;
@endphp

<section class="tt-dashboard" id="dashboard-overview" aria-label="Turance business dashboard">
    <div class="tt-dashboard-intro">
        <div><span class="eyebrow">Business performance</span><h2>Keep the next move visible.</h2></div>
        <div class="tt-dashboard-status"><span class="status-dot status-dot--{{ $visitTrackingReady ? 'live' : 'pending' }}"></span>{{ $visitTrackingReady ? 'Tracking live' : 'Tracking pending setup' }}@if (! empty($anniversaryPromo['is_active']))<span class="tt-status-divider"></span>{{ $anniversaryPromo['discount_percent'] }}% promotion live @endif</div>
    </div>

    <div class="tt-metric-band" aria-label="Business metrics">
        @foreach ($kpiCards as $card)
            <div class="tt-metric"><span class="metric-label">{{ $card['label'] }}</span><strong>{{ $card['value'] }}</strong><span class="tt-metric-trend tt-metric-trend--{{ $card['trend']['direction'] }}">{{ $card['trend']['label'] }}</span></div>
        @endforeach
    </div>

    <div class="tt-analytics-grid">
        <section class="tt-section tt-performance">
            <div class="tt-section-head">
                <div><span class="eyebrow">Performance</span><h2>Activity trend</h2></div>
                <div class="tt-chart-tools"><span>{{ now()->copy()->subDays($dashboardPeriodDays - 1)->format('M d') }} — {{ now()->format('M d, Y') }}</span><nav class="dashboard-chart-periods" aria-label="Chart period">@foreach ($dashboardPeriods as $periodDays => $periodLabel)<a class="{{ $dashboardPeriodDays === $periodDays ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['period' => $periodDays]) }}">{{ $periodLabel }}</a>@endforeach</nav></div>
            </div>
            <div class="tt-chart-legend" aria-label="Chart legend"><span><i class="tt-legend-dot tt-legend-dot--visits"></i>Visits</span><span><i class="tt-legend-dot tt-legend-dot--quotes"></i>Invoices</span><span><i class="tt-legend-dot tt-legend-dot--messages"></i>Leads</span></div>
            @include('admin.quotes.partials.activity-chart', ['chartOnly' => true])
        </section>

        <section class="tt-section tt-pipeline" aria-label="Monthly value pipeline">
            <div class="tt-section-head"><div><span class="eyebrow">Pipeline</span><h2>Commercial value</h2></div><a class="panel-head__link" href="{{ route('admin.quotes.insights') }}">Details</a></div>
            <div class="tt-pipeline-ring" style="--pipeline-progress: {{ $pipelineProgress }}%;"><div class="tt-pipeline-ring__inner"><strong>${{ number_format($totalPipeline, 0) }}</strong><span>Total pipeline</span></div></div>
            <div class="tt-pipeline-breakdown"><div><span>Invoices</span><strong>${{ number_format($totalPipeline, 0) }}</strong></div><div><span>This month</span><strong>${{ number_format($currentPipeline, 0) }}</strong></div><div><span>Average value</span><strong>${{ number_format($averageQuoteValue, 0) }}</strong></div></div>
        </section>

        <aside class="tt-section tt-snapshot">
            <div class="tt-section-head"><div><span class="eyebrow">This month</span><h2>Business snapshot</h2></div></div>
            <div class="tt-snapshot-list"><div class="tt-snapshot-item tt-snapshot-item--gold"><span>Visits</span><strong>{{ number_format($visitsThisMonth) }}</strong><small>{{ $visitTrackingReady ? 'Tracked this month' : 'Awaiting tracking' }}</small></div><div class="tt-snapshot-item tt-snapshot-item--cream"><span>Invoiced</span><strong>${{ number_format($currentPipeline, 0) }}</strong><small>{{ $quotesThisMonth }} {{ Str::plural('invoice', $quotesThisMonth) }} this month</small></div><div class="tt-snapshot-item tt-snapshot-item--gray"><span>Leads</span><strong>{{ number_format($messagesThisMonth) }}</strong><small>{{ $messagesThisMonth ? 'New enquiries' : 'No new enquiries' }}</small></div></div>
        </aside>
    </div>

    <div class="tt-lower-grid">
        <section class="tt-section tt-invoices">
            <div class="tt-section-head"><div><span class="eyebrow">Latest output</span><h2>Recent invoices</h2></div><a class="panel-head__link" href="{{ route('admin.quotes.archive') }}">View all invoices →</a></div>
            <div class="tt-invoice-table-wrap"><table class="tt-invoice-table"><thead><tr><th>Invoice</th><th>Client</th><th>Date</th><th>Amount</th><th>Status</th><th>Project</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody>
                @forelse ($quotes->take(6) as $quote)
                    <tr><td><a class="tt-invoice-number" href="{{ route('admin.quotes.show', $quote) }}">{{ $quote->quote_number }}</a></td><td><strong>{{ $quote->company_name ?: $quote->recipient_name ?: 'Private client' }}</strong><span>{{ $quote->recipient_name ?: '—' }}</span></td><td>{{ optional($quote->created_at)->format('M d, Y') }}</td><td class="tt-amount">${{ number_format((float) $quote->investment_amount, 0) }}</td><td><span class="tt-status-label">Issued</span></td><td>{{ $quote->project_title ?: '—' }}</td><td><details class="tt-row-menu"><summary aria-label="Actions for {{ $quote->quote_number }}">•••</summary><div><a href="{{ route('admin.quotes.show', $quote) }}">Open</a><a href="{{ route('admin.quotes.edit', $quote) }}">Edit</a></div></details></td></tr>
                @empty
                    <tr><td colspan="7"><div class="tt-empty"><strong>Your first invoice will appear here.</strong><span>Create an invoice to start building your pipeline.</span><a class="panel-head__link" href="{{ route('admin.quotes.create') }}">Create invoice →</a></div></td></tr>
                @endforelse
            </tbody></table></div>
        </section>

        <aside class="tt-section tt-projects">
            <div class="tt-section-head"><div><span class="eyebrow">Delivery</span><h2>Active projects</h2></div>@if (\App\Support\AdminAccess::can('projects'))<a class="panel-head__link" href="{{ route('admin.project-management.projects') }}">View all</a>@endif</div>
            <div class="tt-project-list">
                @forelse ($activeProjects as $project)
                    <a class="tt-project" href="{{ route('admin.project-management.projects.show', $project) }}"><div><strong>{{ $project->name }}</strong><span>{{ $project->client_company ?: $project->client_name ?: 'Internal project' }}</span></div><strong>{{ $project->progress_percentage }}%</strong><div class="tt-project-progress"><span style="width: {{ $project->progress_percentage }}%"></span></div><small>{{ Str::headline($project->status) }}@if ($project->ends_on) · due {{ $project->ends_on->format('M d') }}@endif</small></a>
                @empty
                    <div class="tt-empty tt-empty--small"><strong>No active projects yet.</strong><span>Projects will appear here as delivery work begins.</span></div>
                @endforelse
            </div>
            <div class="tt-rail-divider"></div><div class="tt-rail-utility"><span class="eyebrow">Quick access</span><a href="{{ route('admin.quotes.activity') }}">Review activity <span>→</span></a><a href="{{ route('admin.quotes.insights') }}">Open insights <span>→</span></a></div>
        </aside>
    </div>
</section>
