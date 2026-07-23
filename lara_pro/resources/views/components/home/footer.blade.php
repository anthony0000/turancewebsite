<footer class="tt-footer">
    <div class="tt-footer__network" aria-hidden="true"></div>
    <div class="tt-section__inner">
        <div class="tt-footer__top">
            <div class="tt-footer__brand">
                <a href="{{ route('home') }}" aria-label="Turance Technologies home">
                    <img src="{{ asset('/assets/img/logo/logo.png') }}" width="694" height="178" alt="Turance Technologies">
                </a>
                <span class="tt-footer__tagline">{{ config('seo.tagline', 'Excellence Delivered') }}</span>
                <p>Software and digital products shaped with clarity, precision and lasting purpose.</p>
            </div>
            <div class="tt-footer__links">
                <nav aria-label="Footer navigation">
                    <span>Explore</span>
                    <a href="{{ route('home') }}#about">About Us</a>
                    <a href="{{ route('home') }}#services">Services</a>
                    <a href="{{ route('home') }}#work">Work</a>
                    <a href="{{ route('home') }}#insights">Insights</a>
                    <a href="{{ route('contact.show') }}">Contact</a>
                </nav>
                <nav aria-label="Footer services">
                    <span>Services</span>
                    <a href="{{ route('services.web') }}">Web development</a>
                    <a href="{{ route('services.mobile') }}">Mobile products</a>
                    <a href="{{ route('services.saas') }}">SaaS platforms</a>
                    <a href="{{ route('services.branding') }}">Branding &amp; identity</a>
                </nav>
                <address>
                    <span>Connect</span>
                    <a href="mailto:{{ config('seo.email') }}">{{ config('seo.email') }}</a>
                    <a href="tel:{{ preg_replace('/\s+/', '', config('seo.phone')) }}">{{ config('seo.phone') }}</a>
                    <p>{{ config('seo.address.city') }}, {{ config('seo.address.region') }} &middot; {{ config('seo.address.country') }}</p>
                    <small>Social profiles available on request</small>
                </address>
            </div>
        </div>
        <div class="tt-footer__bottom">
            <p>&copy; {{ date('Y') }} Turance Technologies. All rights reserved.</p>
            <div><a href="{{ route('privacy.show') }}">Privacy policy</a><a href="{{ route('terms.show') }}">Terms</a></div>
            <a class="tt-footer__top-link" href="#main-content">Back to top
                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 16V4M5 9l5-5 5 5" /></svg>
            </a>
        </div>
    </div>
</footer>
