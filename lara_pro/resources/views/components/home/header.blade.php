@php($homeAnchor = fn (string $anchor) => request()->routeIs('home') ? '#'.$anchor : route('home').'#'.$anchor)

<header class="tt-header" aria-label="Primary header">
    <a class="tt-header__brand" href="{{ route('home') }}" aria-label="Turance Technologies home">
        <img src="{{ asset('/assets/img/logo/logo.png') }}" width="694" height="178"
            alt="Turance Technologies">
    </a>

    <nav class="tt-header__nav" aria-label="Primary navigation">
        <a href="{{ $homeAnchor('about') }}" data-nav-link>About Us</a>
        <a href="{{ $homeAnchor('services') }}" data-nav-link>Services</a>
        <a href="{{ $homeAnchor('work') }}" data-nav-link>Work</a>
        <a href="{{ $homeAnchor('insights') }}" data-nav-link>Insights</a>
        <a href="{{ request()->routeIs('home') ? '#contact' : route('contact.show') }}" data-nav-link>Contact</a>
    </nav>

    <div class="tt-header__actions">
        <a class="tt-header__inquiries" href="{{ request()->routeIs('home') ? '#contact' : route('contact.show').'#contact-form' }}">
            <span>Inquiries</span>
            <svg viewBox="0 0 20 20" aria-hidden="true">
                <path d="M5 15 15 5M7 5h8v8" />
            </svg>
        </a>

        <button class="tt-menu-button" type="button" aria-label="Open navigation menu"
            aria-expanded="false" aria-controls="tt-mobile-navigation" data-menu-open data-magnetic>
            <span></span><span></span><span></span><span></span>
        </button>
    </div>

    <x-home.mobile-navigation />
</header>
