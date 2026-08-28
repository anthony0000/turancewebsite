<section class="pm-daily-motivation" aria-labelledby="daily-motivation-title">
    <div class="pm-daily-motivation__mark" aria-hidden="true">
        <svg viewBox="0 0 32 32" role="presentation"><path d="M16 3v7M16 22v7M3 16h7M22 16h7M6.8 6.8l4.9 4.9M20.3 20.3l4.9 4.9M25.2 6.8l-4.9 4.9M11.7 20.3l-4.9 4.9"/><circle cx="16" cy="16" r="4.5"/></svg>
    </div>
    <div class="pm-daily-motivation__content">
        <div class="pm-daily-motivation__eyebrow"><span id="daily-motivation-title">Daily motivation</span><span>{{ $dailyMotivation['greeting'] }}</span><span>{{ $dailyMotivation['date'] }}</span></div>
        <blockquote>&ldquo;{{ $dailyMotivation['quote'] }}&rdquo;</blockquote>
        <cite>{{ $dailyMotivation['attribution'] }}</cite>
    </div>
    <div class="pm-daily-motivation__orbit" aria-hidden="true"><span></span><span></span><span></span></div>
</section>
