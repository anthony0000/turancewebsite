@extends('layouts.master')

@section('minimal_page', 'true')

@php
    $services = [
        [
            'key' => 'web',
            'route' => 'services.web',
            'number' => '01',
            'label' => 'Digital presence',
            'outcome' => 'Credibility, conversion and a stronger first impression.',
            'promise' => 'Websites that earn attention and action.',
        ],
        [
            'key' => 'mobile',
            'route' => 'services.mobile',
            'number' => '02',
            'label' => 'Mobile products',
            'outcome' => 'Focused journeys people understand from the first tap.',
            'promise' => 'Mobile products clear from the first tap.',
        ],
        [
            'key' => 'saas',
            'route' => 'services.saas',
            'number' => '03',
            'label' => 'Scalable platforms',
            'outcome' => 'Connected workflows that create control and leverage.',
            'promise' => 'Platforms that turn complexity into control.',
        ],
        [
            'key' => 'branding',
            'route' => 'services.branding',
            'number' => '04',
            'label' => 'Brand systems',
            'outcome' => 'Recognition, clarity and consistency across every touchpoint.',
            'promise' => 'Identity systems built for recognition.',
        ],
    ];

    $process = [
        ['name' => 'Discover', 'copy' => 'Understand the business, audience, constraints and the outcome worth pursuing.'],
        ['name' => 'Define', 'copy' => 'Turn context into priorities, a focused scope and a delivery path everyone can see.'],
        ['name' => 'Design & build', 'copy' => 'Create, test and engineer the experience as one coherent product system.'],
        ['name' => 'Launch & improve', 'copy' => 'Deploy with care, measure real use and keep the strongest opportunities moving.'],
    ];

    $plans = [
        [
            'name' => 'Essential Presence',
            'price' => 'From $1,500',
            'fit' => 'A focused, premium digital presence for founders and growing businesses.',
            'timeline' => '2–4 weeks',
            'items' => ['Discovery and direction', 'Landing page or focused website', 'Responsive build and enquiry flow', 'SEO foundation and launch support'],
        ],
        [
            'name' => 'Growth Build',
            'price' => 'From $6,000',
            'fit' => 'A custom product experience with deeper journeys, workflows or integrations.',
            'timeline' => '5–12 weeks',
            'featured' => true,
            'items' => ['Strategy and UX mapping', 'Custom interface and development', 'Dashboards, integrations or automation', 'Analytics and post-launch refinement'],
        ],
        [
            'name' => 'Flagship System',
            'price' => 'From $12,000',
            'fit' => 'An ambitious multi-surface platform or complete brand and product ecosystem.',
            'timeline' => '12+ weeks',
            'items' => ['End-to-end product direction', 'Advanced UX and technical architecture', 'Connected brand and digital systems', 'Priority launch and growth planning'],
        ],
    ];

    $faqs = [
        ['question' => 'How do we choose the right engagement?', 'answer' => 'We use your goals, timeline, current assets and technical needs to shape the right level of work. The first conversation is about clarity, not forcing a package.'],
        ['question' => 'Can you improve an existing brand or product?', 'answer' => 'Yes. We identify what already works, preserve the strongest foundations and focus investment on the areas limiting clarity, adoption or growth.'],
        ['question' => 'Do you handle both design and development?', 'answer' => 'Yes. Strategy, product design, engineering, testing and launch can stay aligned under one delivery team instead of being divided across several vendors.'],
        ['question' => 'What happens after launch?', 'answer' => 'We can support optimisation, analytics review, maintenance and new feature delivery. Launch is the beginning of real-world learning, not the end of the relationship.'],
    ];
@endphp

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&amp;family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-reference.css') }}?v=2.2">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-sections.css') }}?v=2.1">
    <link rel="stylesheet" href="{{ asset('/assets/css/services-overview-reference.css') }}?v=1.1">
@endpush

@section('content')
    <a class="tt-skip-link" href="#main-content">Skip to main content</a>

    <main class="tt-reference-home tt-services-overview" id="main-content">
        <x-home.header />

        <section class="tt-services-overview-hero" aria-labelledby="services-overview-title">
            <div class="tt-services-overview-hero__mesh" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
            <div class="tt-section__inner tt-services-overview-hero__inner">
                <div class="tt-services-overview-hero__copy">
                    <nav class="tt-services-overview-breadcrumb" aria-label="Breadcrumb">
                        <a href="{{ route('home') }}">Home</a><span>/</span>
                        <span aria-current="page">Services</span>
                    </nav>
                    <p class="tt-services-overview-kicker">Strategy <span>&middot;</span> Design <span>&middot;</span> Engineering</p>
                    <h1 id="services-overview-title">Build what moves your business forward.</h1>
                    <p class="tt-services-overview-hero__lead">
                        From identity and websites to apps and platforms, we connect strategy, design and engineering
                        around one outcome: lasting progress.
                    </p>
                    <div class="tt-services-overview-hero__actions">
                        <x-home.primary-button :href="route('contact.show').'#contact-form'">Start a project</x-home.primary-button>
                        <a class="tt-text-link" href="#capabilities">Explore capabilities
                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M10 3v14M5 12l5 5 5-5" /></svg>
                        </a>
                    </div>
                </div>

                <div class="tt-capability-deck" aria-label="Choose a Turance Technologies capability">
                    <div class="tt-capability-deck__top">
                        <span>Capabilities</span>
                        <span>04 disciplines</span>
                    </div>
                    <p class="tt-capability-deck__prompt">Choose a discipline</p>
                    <nav class="tt-capability-deck__list" aria-label="Service pages">
                        @foreach ($services as $service)
                            @php($detail = config('service-pages.'.$service['key']))
                            <a class="tt-capability-deck__item" href="{{ route($service['route']) }}">
                                <span class="tt-capability-deck__number">{{ $service['number'] }}</span>
                                <span class="tt-capability-deck__service">
                                    <strong>{{ $detail['name'] }}</strong>
                                    <small>{{ $service['promise'] }}</small>
                                </span>
                                <span class="tt-capability-deck__arrow" aria-hidden="true">
                                    <svg viewBox="0 0 20 20"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                                </span>
                            </a>
                        @endforeach
                    </nav>
                    <div class="tt-capability-deck__foot">
                        <span>One team</span>
                        <span>Shaped around your goal</span>
                    </div>
                    <span class="tt-capability-deck__watermark" aria-hidden="true">04</span>
                </div>
            </div>

            <dl class="tt-section__inner tt-services-overview-hero__stats">
                <div><dt>Core disciplines</dt><dd>04</dd></div>
                <div><dt>Delivery model</dt><dd>One integrated team</dd></div>
                <div><dt>Coverage</dt><dd>Strategy to launch</dd></div>
            </dl>
        </section>

        <section class="tt-services-capabilities tt-section" id="capabilities" aria-labelledby="capabilities-title">
            <div class="tt-section__inner">
                <x-home.section-heading eyebrow="Core capabilities" id="capabilities-title"
                    title="Specialist depth, connected around the outcome."
                    copy="Engage one discipline or combine them into a complete brand and product delivery team." />

                <div class="tt-services-capabilities__grid">
                    @foreach ($services as $service)
                        @php($detail = config('service-pages.'.$service['key']))
                        <article class="tt-capability-card tt-capability-card--{{ $loop->iteration }}" data-reveal>
                            <div class="tt-capability-card__top">
                                <span>{{ $service['number'] }}</span>
                                <small>{{ $service['label'] }}</small>
                            </div>
                            <h2>{{ $detail['name'] }}</h2>
                            <p>{{ $detail['lead'] }}</p>
                            <ul>
                                @foreach ($detail['highlights'] as $highlight)
                                    <li>{{ $highlight }}</li>
                                @endforeach
                            </ul>
                            <div class="tt-capability-card__outcome">
                                <span>Built for</span>
                                <strong>{{ $service['outcome'] }}</strong>
                            </div>
                            <a href="{{ route($service['route']) }}">Explore {{ strtolower($detail['name']) }}
                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-services-process tt-section" aria-labelledby="services-process-title">
            <div class="tt-section__inner">
                <x-home.section-heading theme="dark" eyebrow="How we work" id="services-process-title"
                    title="Calm execution. Visible decisions. No disconnected handoffs."
                    copy="The process adapts to the engagement while keeping priorities, progress and product quality clear." />

                <ol class="tt-services-process__list">
                    @foreach ($process as $step)
                        <li data-reveal>
                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div><h3>{{ $step['name'] }}</h3><p>{{ $step['copy'] }}</p></div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section class="tt-services-pricing tt-section" id="service-pricing" aria-labelledby="pricing-title">
            <div class="tt-section__inner">
                <div class="tt-services-pricing__heading">
                    <div>
                        <p class="tt-services-overview-kicker">Starting points</p>
                        <h2 id="pricing-title">A clear frame for planning the investment.</h2>
                    </div>
                    <p>Every proposal is shaped around scope, complexity and timing. These ranges provide a useful place to begin.</p>
                </div>

                <div class="tt-services-pricing__grid">
                    @foreach ($plans as $plan)
                        <article class="{{ ! empty($plan['featured']) ? 'is-featured' : '' }}" data-reveal>
                            @if (! empty($plan['featured']))<span class="tt-services-pricing__badge">Most requested</span>@endif
                            <small>{{ $plan['name'] }}</small>
                            <h3>{{ $plan['price'] }}</h3>
                            <p>{{ $plan['fit'] }}</p>
                            <ul>
                                @foreach ($plan['items'] as $item)<li>{{ $item }}</li>@endforeach
                            </ul>
                            <div>
                                <span>Typical timeline · {{ $plan['timeline'] }}</span>
                                <a href="{{ route('contact.show') }}#contact-form">Discuss this scope
                                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-section tt-faq tt-services-faq" aria-labelledby="services-faq-title">
            <div class="tt-section__inner tt-faq__layout">
                <div class="tt-faq__intro">
                    <x-home.section-heading eyebrow="Before we begin" id="services-faq-title"
                        title="Useful answers for a better first conversation."
                        copy="Clear expectations make stronger engagements. We can handle anything more specific when we understand the context." />
                    <a class="tt-text-link" href="{{ route('contact.show') }}">Ask another question
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                    </a>
                </div>
                <div class="tt-faq__items" data-accordion>
                    @foreach ($faqs as $faq)
                        @php($faqId = 'services-overview-faq-'.$loop->iteration)
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

        <section class="tt-contact-cta" aria-labelledby="services-contact-title">
            <div class="tt-contact-cta__network" aria-hidden="true"><i></i><i></i><i></i><i></i><span><img src="{{ asset('/assets/img/hero/hero-bg-shape3.png') }}" width="250" height="250" alt=""></span></div>
            <div class="tt-section__inner tt-contact-cta__inner">
                <x-home.section-heading theme="dark" eyebrow="Start a conversation" id="services-contact-title"
                    title="Bring us the ambition. We will help shape the right path."
                    copy="Tell us what you are building, improving or trying to solve. The first useful outcome is clarity." />
                <div class="tt-contact-cta__actions" data-reveal>
                    <x-home.primary-button :href="route('contact.show').'#contact-form'">Start your project</x-home.primary-button>
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
