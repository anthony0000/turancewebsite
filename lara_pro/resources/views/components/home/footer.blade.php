@props(['embedded' => false])
@php($homeAnchor = fn (string $anchor) => request()->routeIs('home') ? '#'.$anchor : route('home').'#'.$anchor)

<footer class="ed-footer{{ $embedded ? '' : ' ed-footer--standalone' }}">
    <div class="ed-footer__brand">
        <a class="ed-footer__wordmark" href="{{ $homeAnchor('top') }}">TURANCE<small>TECHNOLOGIES</small></a>
        <span>{{ config('seo.tagline', 'Excellence Delivered') }}</span>
    </div>
    <div class="ed-footer__right">
        <nav aria-label="Footer navigation">
            <a href="{{ $homeAnchor('work') }}">Work</a>
            <a href="{{ $homeAnchor('services') }}">Services</a>
            <a href="{{ route('contact.show') }}">Contact</a>
        </nav>
        <small>&copy; {{ date('Y') }} Turance Technologies</small>
    </div>
    <div class="ed-footer__utility">
        <a href="{{ route('privacy.show') }}">Privacy</a>
        <a href="{{ route('terms.show') }}">Terms</a>
        <button type="button" data-cookie-preferences>Cookie preferences</button>
    </div>
</footer>
