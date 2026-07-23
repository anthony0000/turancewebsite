@props(['icon', 'title', 'subtitle'])

<article class="tt-service-highlight">
    <span class="tt-service-highlight__icon" aria-hidden="true">
        @if ($icon === 'strategy')
            <svg viewBox="0 0 32 32">
                <path d="m16 3.8 3.7 7.5 8.3 1.2-6 5.8 1.4 8.2-7.4-3.9-7.4 3.9 1.4-8.2-6-5.8 8.3-1.2L16 3.8Z" />
            </svg>
        @elseif ($icon === 'design')
            <svg viewBox="0 0 32 32">
                <path d="m16 3.7 10 5.8v11L16 26.3 6 20.5v-11l10-5.8Z" />
                <path d="m6.4 9.7 9.6 5.5 9.6-5.5M16 15.2v11" />
            </svg>
        @else
            <svg viewBox="0 0 32 32">
                <path d="m18.4 3-9 15h7.1L13.7 29l9.8-15.7h-7L18.4 3Z" />
            </svg>
        @endif
    </span>
    <span class="tt-service-highlight__copy">
        <strong>{{ $title }}</strong>
        <small>{{ $subtitle }}</small>
    </span>
</article>
