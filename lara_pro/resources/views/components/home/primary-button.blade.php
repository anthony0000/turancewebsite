@props(['href'])

<a {{ $attributes->class(['tt-primary-button'])->merge(['href' => $href, 'data-magnetic' => '']) }}>
    <span>{{ $slot }}</span>
    <i aria-hidden="true">
        <svg viewBox="0 0 24 24">
            <path d="M5 12h14M14 7l5 5-5 5" />
        </svg>
    </i>
</a>
