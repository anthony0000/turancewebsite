@extends('layouts.master')

@section('minimal_page', 'true')

@php
    $serviceKey = \Illuminate\Support\Str::after((string) request()->route()->getName(), 'services.');
    $service = config('service-pages.'.$serviceKey);
    abort_unless($service, 404);
    $serviceRoutes = [
        'web' => 'services.web',
        'mobile' => 'services.mobile',
        'saas' => 'services.saas',
        'branding' => 'services.branding',
    ];
@endphp

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&amp;family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-reference.css') }}?v=2.2">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-sections.css') }}?v=2.1">
    <link rel="stylesheet" href="{{ asset('/assets/css/service-detail-reference.css') }}?v=1.0">
@endpush

@section('content')
    <a class="tt-skip-link" href="#main-content">Skip to main content</a>

    <main class="tt-reference-home tt-detail" id="main-content">
        <x-home.header />

        <section class="tt-detail-hero" aria-labelledby="service-title">
            <div class="tt-detail-hero__linework" aria-hidden="true"><i></i><i></i><i></i><span></span></div>
            <span class="tt-detail-hero__word" aria-hidden="true">{{ $service['hero_word'] }}</span>
            <div class="tt-section__inner tt-detail-hero__inner">
                <div class="tt-detail-hero__copy">
                    <nav class="tt-detail-breadcrumb" aria-label="Breadcrumb">
                        <a href="{{ route('home') }}">Home</a><span>/</span>
                        <a href="{{ route('service.show') }}">Services</a><span>/</span>
                        <span aria-current="page">{{ $service['name'] }}</span>
                    </nav>
                    <p class="tt-detail-kicker">{{ $service['eyebrow'] }}</p>
                    <h1 id="service-title">{{ $service['title'] }}</h1>
                    <p class="tt-detail-hero__lead">{{ $service['lead'] }}</p>
                    @if (! empty($service['hero_disciplines']))
                        <ul class="tt-detail-hero__disciplines" aria-label="Branding capabilities">
                            @foreach ($service['hero_disciplines'] as $discipline)
                                <li>{{ $discipline }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="tt-detail-hero__actions">
                        <x-home.primary-button :href="route('contact.show').'#contact-form'">Start a project</x-home.primary-button>
                        <a class="tt-text-link" href="#approach">Explore our approach
                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 3v14M5 12l5 5 5-5" /></svg>
                        </a>
                    </div>
                    <dl class="tt-detail-meta">
                        <div><dt>Typical engagement</dt><dd>{{ $service['engagement'] }}</dd></div>
                        <div><dt>Designed for</dt><dd>{{ $service['fit'] }}</dd></div>
                    </dl>
                </div>
                <figure class="tt-detail-hero__visual" data-reveal>
                    <div class="tt-detail-hero__backplate" aria-hidden="true">
                        <span>{{ $service['visual_code'] }}</span>
                        <i></i><i></i><i></i>
                    </div>
                    <div class="tt-detail-hero__frame">
                        <img src="{{ asset($service['image']) }}" alt="{{ $service['image_alt'] }}">
                        <span class="tt-detail-hero__frame-index" aria-hidden="true">
                            {{ str_pad(array_search($serviceKey, array_keys($serviceRoutes), true) + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <aside class="tt-detail-hero__specimen">
                        <div>
                            <small>Core system</small>
                            <strong>{{ $service['name'] }}</strong>
                        </div>
                        <ol>
                            @foreach ($service['visual_details'] as $detail)
                                <li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $detail }}</li>
                            @endforeach
                        </ol>
                        <div class="tt-detail-hero__palette" aria-label="Service colour palette">
                            <i></i><i></i><i></i><i></i>
                        </div>
                    </aside>
                    <figcaption><span>{{ $service['visual_code'] }}</span>One considered system</figcaption>
                </figure>
            </div>
        </section>

        <section class="tt-detail-overview tt-section" id="approach" aria-labelledby="overview-title">
            <div class="tt-section__inner tt-detail-overview__grid">
                <div class="tt-detail-overview__heading">
                    <p class="tt-detail-kicker">The opportunity</p>
                    <h2 id="overview-title">{{ $service['overview_title'] }}</h2>
                </div>
                <div class="tt-detail-overview__body" data-reveal>
                    @foreach ($service['overview'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                    <ul>
                        @foreach ($service['highlights'] as $highlight)
                            <li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $highlight }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        <section class="tt-detail-process tt-section" aria-labelledby="process-title">
            <div class="tt-section__inner">
                <x-home.section-heading theme="dark" eyebrow="How we work" id="process-title"
                    title="A disciplined path from context to a confident outcome."
                    copy="Every engagement is shaped around the problem, but the work stays visible, collaborative and grounded in clear decisions." />
                <ol class="tt-detail-process__grid">
                    @foreach ($service['process'] as $step)
                        <li data-reveal>
                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $step['name'] }}</h3>
                            <p>{{ $step['copy'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="tt-detail-deliverables tt-section" aria-labelledby="deliverables-title">
            <div class="tt-section__inner tt-detail-deliverables__grid">
                <div>
                    <p class="tt-detail-kicker">What comes together</p>
                    <h2 id="deliverables-title">A complete system, not a collection of disconnected outputs.</h2>
                    <p>The final scope is tailored to the engagement. These are the capabilities most commonly brought together for {{ strtolower($service['name']) }}.</p>
                </div>
                <ul data-reveal>
                    @foreach ($service['deliverables'] as $deliverable)
                        <li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $deliverable }}</li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="tt-detail-related tt-section" aria-labelledby="related-title">
            <div class="tt-section__inner">
                <div class="tt-detail-related__heading">
                    <div>
                        <p class="tt-detail-kicker">Connected capabilities</p>
                        <h2 id="related-title">Explore the rest of our expertise.</h2>
                    </div>
                    <a class="tt-text-link" href="{{ route('service.show') }}">View all services
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                    </a>
                </div>
                <div class="tt-detail-related__grid">
                    @foreach ($serviceRoutes as $key => $routeName)
                        @continue($key === $serviceKey)
                        @php($related = config('service-pages.'.$key))
                        <a href="{{ route($routeName) }}" data-reveal>
                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $related['name'] }}</h3>
                            <p>{{ $related['lead'] }}</p>
                            <b>Explore service <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg></b>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-section tt-faq tt-detail-faq" aria-labelledby="faq-title">
            <div class="tt-section__inner tt-faq__layout">
                <div class="tt-faq__intro">
                    <x-home.section-heading eyebrow="Before we begin" id="faq-title"
                        title="A few useful answers."
                        copy="The right scope starts with the right context. We can answer anything more specific in an initial conversation." />
                    <a class="tt-text-link" href="{{ route('contact.show') }}">Ask another question
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                    </a>
                </div>
                <div class="tt-faq__items" data-accordion>
                    @foreach ($service['faqs'] as $faq)
                        @php($faqId = 'service-faq-'.$serviceKey.'-'.$loop->iteration)
                        <article class="tt-faq__item {{ $loop->first ? 'is-open' : '' }}">
                            <h3>
                                <button type="button" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="{{ $faqId }}">
                                    <span><i>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</i>{{ $faq['question'] }}</span>
                                    <b aria-hidden="true"></b>
                                </button>
                            </h3>
                            <div class="tt-faq__answer" id="{{ $faqId }}" role="region" @unless($loop->first) hidden @endunless>
                                <div><p>{{ $faq['answer'] }}</p></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-contact-cta" aria-labelledby="contact-title">
            <div class="tt-contact-cta__network" aria-hidden="true"><i></i><i></i><i></i><i></i><span><img src="{{ asset('/assets/img/hero/hero-bg-shape3.png') }}" width="250" height="250" alt=""></span></div>
            <div class="tt-section__inner tt-contact-cta__inner">
                <x-home.section-heading theme="dark" eyebrow="Start a conversation" id="contact-title"
                    title="Ready to shape this properly?"
                    copy="Tell us where the opportunity is, what is getting in the way and what a strong result should make possible." />
                <div class="tt-contact-cta__actions" data-reveal>
                    <x-home.primary-button :href="route('contact.show').'#contact-form'">Discuss your project</x-home.primary-button>
                    <a class="tt-contact-cta__secondary" href="mailto:{{ config('seo.email') }}">Contact our team
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                    </a>
                </div>
            </div>
        </section>

        <x-home.footer />
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('/assets/js/home-reference.js') }}?v=2.3" defer></script>
@endpush
