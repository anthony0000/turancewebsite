@php($homeAnchor = fn (string $anchor) => request()->routeIs('home') ? '#'.$anchor : route('home').'#'.$anchor)

<div class="tt-menu-backdrop" data-menu-backdrop hidden></div>

<aside class="tt-mobile-navigation" id="tt-mobile-navigation" aria-hidden="true" aria-label="Navigation menu"
    data-mobile-navigation inert>
    <div class="tt-mobile-navigation__top">
        <a class="tt-mobile-navigation__brand" href="{{ route('home') }}" aria-label="Turance Technologies home">
            <img src="{{ asset('/assets/img/logo/logo.png') }}" width="694" height="178" alt="">
        </a>

        <div class="tt-mobile-navigation__count" aria-hidden="true">
            <span>Menu</span>
            <small>01 — 05</small>
        </div>

        <button type="button" aria-label="Close navigation menu" data-menu-close>
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="m6 6 12 12M18 6 6 18" />
            </svg>
        </button>
    </div>

    <div class="tt-mobile-navigation__body">
        <div class="tt-mobile-navigation__intro">
            <span class="tt-mobile-navigation__eyebrow"><i aria-hidden="true"></i> Navigation</span>
            <p>Explore our studio, capabilities and thinking.</p>
        </div>

        <nav aria-label="Menu navigation">
            <a href="{{ $homeAnchor('about') }}" data-nav-link>
                <span class="tt-mobile-navigation__index" aria-hidden="true">01</span>
                <span class="tt-mobile-navigation__link-copy">
                    <span class="tt-mobile-navigation__label">About Us</span>
                    <small>The people and purpose behind our work</small>
                </span>
                <svg class="tt-mobile-navigation__arrow" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14M14 7l5 5-5 5" />
                </svg>
            </a>
            <a href="{{ $homeAnchor('services') }}" data-nav-link>
                <span class="tt-mobile-navigation__index" aria-hidden="true">02</span>
                <span class="tt-mobile-navigation__link-copy">
                    <span class="tt-mobile-navigation__label">Services</span>
                    <small>Strategy, design and digital development</small>
                </span>
                <svg class="tt-mobile-navigation__arrow" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14M14 7l5 5-5 5" />
                </svg>
            </a>
            <a href="{{ $homeAnchor('work') }}" data-nav-link>
                <span class="tt-mobile-navigation__index" aria-hidden="true">03</span>
                <span class="tt-mobile-navigation__link-copy">
                    <span class="tt-mobile-navigation__label">Work</span>
                    <small>Selected products and brand experiences</small>
                </span>
                <svg class="tt-mobile-navigation__arrow" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14M14 7l5 5-5 5" />
                </svg>
            </a>
            <a href="{{ $homeAnchor('insights') }}" data-nav-link>
                <span class="tt-mobile-navigation__index" aria-hidden="true">04</span>
                <span class="tt-mobile-navigation__link-copy">
                    <span class="tt-mobile-navigation__label">Insights</span>
                    <small>Ideas on technology, brands and growth</small>
                </span>
                <svg class="tt-mobile-navigation__arrow" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14M14 7l5 5-5 5" />
                </svg>
            </a>
            <a href="{{ request()->routeIs('home') ? '#contact' : route('contact.show') }}" data-nav-link>
                <span class="tt-mobile-navigation__index" aria-hidden="true">05</span>
                <span class="tt-mobile-navigation__link-copy">
                    <span class="tt-mobile-navigation__label">Contact</span>
                    <small>Tell us what you are ready to build</small>
                </span>
                <svg class="tt-mobile-navigation__arrow" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M5 12h14M14 7l5 5-5 5" />
                </svg>
            </a>
        </nav>
    </div>

    <div class="tt-mobile-navigation__footer">
        <div class="tt-mobile-navigation__footer-copy">
            <span>Have a project in mind?</span>
            <small>New projects and partnerships</small>
        </div>

        <a class="tt-mobile-navigation__inquiry" href="{{ route('contact.show') }}"
            data-conversion="mobile_menu_quote">
            Get a project estimate
            <svg viewBox="0 0 20 20" aria-hidden="true">
                <path d="M5 15 15 5M7 5h8v8" />
            </svg>
        </a>
    </div>
</aside>
