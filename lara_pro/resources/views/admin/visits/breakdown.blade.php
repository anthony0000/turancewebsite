<section class="panel va-card">
    <div class="va-section-head"><div><span class="eyebrow">{{ $eyebrow }}</span><h2>{{ $heading }}</h2><p>{{ $description }}</p></div></div>
    <div class="va-ranking">
        @forelse ($rows as $row)
            @php
                $share = $total ? round($row['count'] / $total * 100, 1) : 0;
            @endphp
            <div class="va-ranking-row">
                <div class="va-ranking-label">
                    @if (! empty($filterKey) && $row['label'] !== 'Uncategorised')
                        <a href="{{ route('admin.visits.index', array_merge($filters, [$filterKey => $row['label']])) }}">{{ $row['label'] }}</a>
                    @else
                        <span>{{ $row['label'] }}</span>
                    @endif
                    <strong>{{ number_format($row['count']) }} <small>{{ $share }}%</small></strong>
                </div>
                <div class="va-track" aria-hidden="true"><span style="width: {{ $share }}%"></span></div>
            </div>
        @empty
            <p class="va-empty-copy">No matching views in this period.</p>
        @endforelse
    </div>
</section>
