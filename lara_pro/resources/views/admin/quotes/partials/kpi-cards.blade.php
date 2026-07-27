<section class="kpi-grid" aria-label="Key metrics">
    @foreach ($kpiCards as $card)
        <article class="kpi-card kpi-card--{{ $card['tone'] }}">
            <span class="metric-label">{{ $card['label'] }}</span>

            <div class="kpi-figure">
                <strong class="kpi-value">{{ $card['value'] }}</strong>
                <span class="trend-pill trend-pill--{{ $card['trend']['direction'] }}">{{ $card['trend']['label'] }}</span>
            </div>

            <p class="kpi-hint">{{ $card['hint'] }}</p>
            <span class="kpi-context">{{ $card['trend']['context'] }}</span>
        </article>
    @endforeach
</section>
