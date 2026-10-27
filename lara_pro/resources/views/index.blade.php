@extends('layouts.master')
@section('minimal_page', 'true')
@section('seo_description', 'Turance Technologies designs and builds thoughtful websites, digital products, mobile applications and software for ambitious teams.')
@push('styles')
<link rel="preload" as="font" type="font/ttf" crossorigin href="{{ asset('/assets/fonts/Urbanist-Regular.ttf') }}">
<link rel="preload" as="image" media="(min-width: 901px)" href="{{ asset('/assets/img/hero/turance-editorial-reference.png') }}" fetchpriority="high">
<link rel="preload" as="image" media="(max-width: 900px)" href="{{ asset('/assets/img/hero/turance-editorial-ribbon.png') }}" fetchpriority="high">
<link rel="stylesheet" href="{{ asset('/assets/css/editorial-home.css') }}?v=1.2">
@endpush
@push('scripts')
<script src="{{ asset('/assets/js/editorial-home.js') }}?v=1.1" defer></script>
@endpush
@section('content')
@php
    $projects = array_values(config('portfolio.projects'));
    $services = [
        ['name' => 'Digital product design', 'detail' => 'Research / Experience / Interface', 'route' => 'service.show'],
        ['name' => 'Websites & platforms', 'detail' => 'Digital presence / Web applications', 'route' => 'services.web'],
        ['name' => 'Mobile applications', 'detail' => 'iOS / Android / Cross-platform', 'route' => 'services.mobile'],
        ['name' => 'Custom software & automation', 'detail' => 'Systems / Integrations / Workflows', 'route' => 'services.saas'],
        ['name' => 'Branding & identity', 'detail' => 'Positioning / Visual systems', 'route' => 'services.branding'],
    ];
    $steps = [
        ['name' => 'Discover', 'detail' => 'Understand the people, the challenge and the opportunity.'],
        ['name' => 'Define', 'detail' => 'Give the work a clear direction and practical scope.'],
        ['name' => 'Design', 'detail' => 'Shape a useful, considered experience.'],
        ['name' => 'Develop', 'detail' => 'Build, test and refine the product.'],
        ['name' => 'Deliver', 'detail' => 'Launch carefully and keep improving.'],
    ];
    $anniversaryPromo = app(\App\Support\AnniversaryPromotion::class)->current();
@endphp
<a class="ed-skip" href="#main-content">Skip to content</a>
<div class="ed-site" id="top">
    <header class="ed-header" data-header>
        <a class="ed-brand" href="{{ route('home') }}" aria-label="Turance Technologies home"><img src="{{ asset('/assets/img/logo/logo.png') }}" width="694" height="178" alt="" decoding="async"><span>TURANCE<small>TECHNOLOGIES</small></span></a>
        <nav class="ed-nav" id="ed-nav" aria-label="Primary navigation"><a href="#work">Work</a><a href="#services">Services</a><a href="#about">About</a><a class="ed-nav__mobile-contact" href="{{ route('contact.show') }}">Contact <span aria-hidden="true">↗</span></a></nav>
        <a class="ed-header__contact" href="{{ route('contact.show') }}" data-conversion="header_contact">Contact <span aria-hidden="true">✦</span></a>
        <button class="ed-menu" type="button" aria-label="Open menu" aria-controls="ed-nav" aria-expanded="false" data-menu-button><span></span><span></span></button>
    </header>
    <main id="main-content">
        <section class="ed-hero" aria-labelledby="hero-title">
            <div class="ed-hero__art" aria-hidden="true" data-hero-art><picture><source media="(max-width: 900px)" srcset="{{ asset('/assets/img/hero/turance-editorial-ribbon.png') }}"><img src="{{ asset('/assets/img/hero/turance-editorial-reference.png') }}" width="1620" height="6031" alt="" fetchpriority="high" decoding="sync"></picture></div>
            <div class="ed-hero__content">
                <div class="ed-hero__rule" aria-hidden="true"></div>
                <h1 id="hero-title" class="ed-hero__title"><span class="ed-mask"><span>TU<span class="ed-gold">RA</span>NCE</span></span><small>TECHNOLOGIES</small></h1>
                <p class="ed-hero__tagline">Digital experiences<br>with purpose.</p>
                <a class="ed-circle-link ed-hero__cta" href="{{ route('contact.show') }}" data-conversion="hero_project"><span class="ed-circle-link__icon" aria-hidden="true">↗</span><span>Start your project</span></a>
            </div>
        </section>
        @if ($anniversaryPromo['is_active'])
        <aside class="ed-promo tt-hero__anniversary tt-reveal" aria-label="Current anniversary offer"><span>{{ $anniversaryPromo['years'] }} years of Turance · {{ $anniversaryPromo['discount_percent'] + 0 }}% anniversary offer</span><a href="{{ route('contact.show', ['promo' => $anniversaryPromo['code']]) }}" data-conversion="anniversary_offer">Explore the offer <span aria-hidden="true">↗</span></a></aside>
        @endif
        <section class="ed-section ed-intro" id="about" aria-labelledby="intro-title">
            <div class="ed-section__top"><span>01 / The studio</span><span>Independent creative technology studio</span></div>
            <div class="ed-intro__grid"><h2 id="intro-title" class="ed-display" data-reveal>Everything<br><em>considered.</em></h2><p data-reveal>We turn complex challenges into beautifully simple digital experiences. Thoughtful in design. Precise in execution.</p></div>
            <div class="ed-section__bottom"><span>Strategy / Design / Technology</span><a href="#work">Scroll to explore <span aria-hidden="true">↓</span></a></div>
        </section>
        <section class="ed-section ed-work" id="work" aria-labelledby="work-title">
            <div class="ed-section__top"><span>02 / Selected work</span><span>A small selection of what we build</span></div>
            <div class="ed-section__heading"><h2 id="work-title" class="ed-display" data-reveal>Made to matter.</h2><p data-reveal>Thoughtful digital products, designed with care and built for real-world use.</p></div>
            <div class="ed-work__grid">
                @foreach ($projects as $project)
                <a class="ed-project" href="{{ route('work.show', ['slug' => $project['slug']]) }}" aria-label="Read about {{ $project['name'] }}" data-reveal>
                    <div class="ed-project__image"><img src="{{ asset($project['image']) }}" alt="{{ $project['image_alt'] }}" width="1440" height="1800" loading="lazy" decoding="async"><span class="ed-project__number">{{ $project['number'] }} / {{ $project['category'] }}</span><span class="ed-project__view">View project ↗</span></div>
                    <div class="ed-project__caption"><span><strong>{{ $project['name'] }}</strong><small>{{ $project['description'] }}</small></span><span aria-hidden="true">↗</span></div>
                </a>
                @endforeach
            </div>
            <div class="ed-more-work" aria-label="More live projects">
                <span>More live work</span>
                <a href="https://www.36plusone.org/" target="_blank" rel="noopener noreferrer">36 Plus One <span aria-hidden="true">↗</span></a>
                <a href="https://kiddovista.co.uk/" target="_blank" rel="noopener noreferrer">KiddoVista <span aria-hidden="true">↗</span></a>
                <a href="https://shop.ihcpro.co.uk/" target="_blank" rel="noopener noreferrer">IHcPro Store <span aria-hidden="true">↗</span></a>
            </div>
            <div class="ed-section__bottom"><span>Every detail has a purpose.</span><a href="{{ route('contact.show') }}">Explore what we can build <span aria-hidden="true">↗</span></a></div>
        </section>
        <section class="ed-section ed-services" id="services" aria-labelledby="services-title">
            <div class="ed-section__top"><span>03 / Capabilities</span><span>From first idea to finished product</span></div>
            <div class="ed-section__heading"><h2 id="services-title" class="ed-display" data-reveal>What we<br>bring to life.</h2><p data-reveal>From the first idea to the finished product, our work brings design thinking and engineering together.</p></div>
            <div class="ed-service-list">
                @foreach ($services as $service)
                <a class="ed-service" href="{{ route($service['route']) }}" data-reveal><span class="ed-service__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $service['name'] }}</strong><small>{{ $service['detail'] }}</small><span class="ed-service__arrow" aria-hidden="true">↗</span></a>
                @endforeach
            </div>
        </section>
        <section class="ed-section ed-process" id="approach" aria-labelledby="process-title">
            <div class="ed-section__top"><span>04 / Our approach</span><span>Clarity from beginning to beyond launch</span></div>
            <h2 id="process-title" class="ed-display" data-reveal>Nothing<br><em>unnecessary.</em></h2>
            <div class="ed-process__intro"><p>The thoughtful details<br>make all the difference.</p><p>No clutter. No complexity for its own sake. Just clarity, craft and technology working together—so every interaction earns its place.</p></div>
            <ol class="ed-process__steps" aria-label="Our process">@foreach ($steps as $step)<li data-reveal><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $step['name'] }}</strong><small>{{ $step['detail'] }}</small></li>@endforeach</ol>
        </section>
        <section class="ed-section ed-credibility" id="perspectives" aria-labelledby="credibility-title">
            <span id="insights" class="ed-anchor-alias" aria-hidden="true"></span>
            <div class="ed-section__top"><span>05 / In practice</span><span>Real products, real purpose</span></div>
            <div class="ed-credibility__grid"><h2 id="credibility-title" class="ed-display" data-reveal>Built for<br><em>real use.</em></h2><div data-reveal><p>Our work is visible in live products—from TailorsMind’s fashion workspace to 3Hjobs’ local services marketplace.</p><a href="#work">See selected work <span aria-hidden="true">↗</span></a></div></div>
        </section>
        <section class="ed-section ed-faq" id="faq" aria-labelledby="faq-title">
            <div class="ed-section__top"><span>Questions / Answers</span><span>Before we begin</span></div>
            <div class="ed-faq__grid"><h2 id="faq-title" class="ed-display" data-reveal>Good to<br><em>know.</em></h2><div class="ed-faq__items">@foreach (config('seo.home_faqs', []) as $faq)<details><summary>{{ $faq['question'] }}<span aria-hidden="true">+</span></summary><p>{{ $faq['answer'] }}</p></details>@endforeach</div></div>
        </section>
        <section class="ed-section ed-contact" id="contact" aria-labelledby="contact-title">
            <div class="ed-section__top"><span>06 / The next chapter</span><span>Have a project in mind?</span></div>
            <h2 id="contact-title" class="ed-display" data-reveal>Something<br><em>remarkable.</em></h2>
            <div class="ed-contact__action"><div><p>Good ideas deserve to become great experiences.<br>Tell us what you’re building.</p><div class="ed-contact__channels"><a href="mailto:{{ config('seo.email') }}">Email us</a><a href="{{ config('seo.whatsapp_url') }}" target="_blank" rel="noopener noreferrer">WhatsApp</a></div></div><a class="ed-circle-link" href="{{ route('contact.show') }}" data-conversion="final_project"><span>Start a conversation</span><span class="ed-circle-link__icon" aria-hidden="true">↗</span></a></div>
            <div class="ed-contact__notice">Proposal materials are confidential. Please review our <a href="{{ route('terms.show') }}#proposal-design-rights">proposal and design rights terms</a> before sharing materials.</div>
            <footer class="ed-footer">
                <div class="ed-footer__brand"><a class="ed-footer__wordmark" href="#top">TURANCE<small>TECHNOLOGIES</small></a><span>Excellence delivered</span></div>
                <div class="ed-footer__right"><nav aria-label="Footer navigation"><a href="#work">Work</a><a href="#services">Services</a><a href="{{ route('contact.show') }}">Contact</a></nav><small>© {{ date('Y') }} Turance Technologies</small></div>
                <div class="ed-footer__utility"><a href="{{ route('privacy.show') }}">Privacy</a><a href="{{ route('terms.show') }}">Terms</a><button type="button" data-cookie-preferences>Cookie preferences</button></div>
            </footer>
        </section>
    </main>
</div>
@endsection
