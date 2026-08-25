@php($compactKpi = $compactKpi ?? false)

<section class="kpi-grid {{ $compactKpi ? 'kpi-grid--compact' : '' }}" aria-label="Key metrics">
    @foreach ($kpiCards as $card)
        <article class="kpi-card kpi-card--{{ $card['tone'] }}" @if ($compactKpi) title="{{ $card['hint'] }}" @endif>
            <span class="metric-label">{{ $card['label'] }}</span>

            <div class="kpi-figure">
                <strong class="kpi-value">{{ $card['value'] }}</strong>
                <span class="trend-pill trend-pill--{{ $card['trend']['direction'] }}">{{ $card['trend']['label'] }}</span>
            </div>

            @unless ($compactKpi)
                <p class="kpi-hint">{{ $card['hint'] }}</p>
            @endunless
            <span class="kpi-context">{{ $card['trend']['context'] }}</span>
        </article>
    @endforeach
</section>
