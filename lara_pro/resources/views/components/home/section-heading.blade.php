@props(['eyebrow', 'title', 'id', 'copy' => null, 'theme' => 'light'])

<header {{ $attributes->class(['tt-section-heading', 'tt-section-heading--dark' => $theme === 'dark']) }}>
    <div class="tt-section-heading__lead" data-reveal>
        <span class="tt-section-heading__eyebrow"><i aria-hidden="true"></i>{{ $eyebrow }}</span>
        <h2 id="{{ $id }}">{{ $title }}</h2>
    </div>

    @if ($copy)
        <p data-reveal>{{ $copy }}</p>
    @endif
</header>
