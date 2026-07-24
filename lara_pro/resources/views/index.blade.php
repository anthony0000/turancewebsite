@extends('layouts.master')

@section('minimal_page', 'true')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&amp;family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-reference.css') }}?v=2.4">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-sections.css') }}?v=2.2">
@endpush

@section('content')
    @php
        $services = [
            ['name' => 'Product Strategy', 'copy' => 'Transform ideas and business requirements into focused digital roadmaps.', 'route' => 'service.show'],
            ['name' => 'UI/UX Design', 'copy' => 'Create intuitive, memorable and conversion-focused digital experiences.', 'route' => 'service.show'],
            ['name' => 'Web Development', 'copy' => 'Build fast, responsive, secure and scalable websites and applications.', 'route' => 'services.web'],
            ['name' => 'Mobile Development', 'copy' => 'Develop high-quality mobile products for modern customer experiences.', 'route' => 'services.mobile'],
            ['name' => 'SaaS & Automation', 'copy' => 'Turn complex operations into scalable products and connected workflows.', 'route' => 'services.saas'],
            ['name' => 'Branding & Identity', 'copy' => 'Build a distinct visual and verbal system that earns recognition and trust.', 'route' => 'services.branding'],
        ];

        $projects = [
            [
                'name' => 'TailorsMind', 'industry' => 'Fashion technology', 'category' => 'Product ecosystem',
                'summary' => 'A connected platform designed to make bespoke fashion discovery, measurement and fulfilment more considered.',
                'services' => ['Product strategy', 'UX design', 'Platform engineering'], 'year' => '2026',
                'outcome' => 'A scalable foundation connecting customers, creators and operations.', 'visual' => 'fashion',
                'image' => null, 'link' => null,
            ],
            [
                'name' => '3Hjobs', 'industry' => 'Local services', 'category' => 'Marketplace platform',
                'summary' => 'A location-aware marketplace that helps people discover and engage trusted artisans around them.',
                'services' => ['Service design', 'Mobile experience', 'Backend development'], 'year' => '2025',
                'outcome' => 'A clearer path from local discovery to service engagement.', 'visual' => 'marketplace',
                'image' => null, 'link' => null,
            ],
            [
                'name' => 'KleanManager', 'industry' => 'Business operations', 'category' => 'Management software',
                'summary' => 'An operations platform bringing orders, customers, payments and daily laundry workflows into one view.',
                'services' => ['Workflow strategy', 'UI/UX design', 'Web application'], 'year' => '2025',
                'outcome' => 'Better operational visibility and more consistent service control.', 'visual' => 'operations',
                'image' => null, 'link' => null,
            ],
            [
                'name' => '36 Plus One', 'industry' => 'Social impact', 'category' => 'Nonprofit platform',
                'summary' => 'A public-facing platform helping a development initiative communicate its mission and connect people with community causes.',
                'services' => ['UX design', 'Web development', 'Donation workflows'], 'year' => 'Live',
                'outcome' => 'One accessible destination for causes, donations, volunteering, organisational work and partner engagement.',
                'visual' => 'impact',
                'image' => null,
                'link' => 'https://www.36plusone.org/',
                'link_label' => 'View live project',
            ],
            [
                'name' => 'KiddoVista', 'industry' => 'Family commerce', 'category' => 'Multi-vendor marketplace',
                'summary' => 'A trusted baby marketplace that brings approved sellers, independent storefronts and essential products into one family-friendly shopping experience.',
                'services' => ['Marketplace UX', 'Vendor management', 'Commerce engineering'], 'year' => 'Live',
                'outcome' => 'A connected destination where families can discover products and shop across trusted seller stores.',
                'visual' => 'family',
                'image' => null,
                'link' => 'https://kiddovista.co.uk/',
                'link_label' => 'View live project',
            ],
            [
                'name' => 'IHcPro Store', 'industry' => 'Healthcare commerce', 'category' => 'E-commerce platform',
                'summary' => 'A UK healthcare storefront designed to make medical supplies, cleaning products and specialist equipment easier to discover and purchase.',
                'services' => ['Commerce strategy', 'UI/UX design', 'E-commerce development'], 'year' => 'Live',
                'outcome' => 'A structured digital shop connecting healthcare customers with essential products and clear purchasing journeys.',
                'visual' => 'healthcare',
                'image' => null,
                'link' => 'https://shop.ihcpro.co.uk/',
                'link_label' => 'View live project',
            ],
        ];

        $process = [
            ['name' => 'Discover', 'copy' => 'Understand the organisation, users, challenges, goals and opportunities.'],
            ['name' => 'Define', 'copy' => 'Translate insight into priorities, requirements and a clear execution plan.'],
            ['name' => 'Design & Build', 'copy' => 'Create, test and develop a refined digital solution.'],
            ['name' => 'Launch & Improve', 'copy' => 'Deploy, monitor, optimise and support long-term growth.'],
        ];

        $capabilities = [
            ['group' => 'Frontend Engineering', 'tools' => ['React', 'Next.js', 'Vue']],
            ['group' => 'Backend & APIs', 'tools' => ['Laravel', 'Node.js', 'REST APIs']],
            ['group' => 'Mobile Applications', 'tools' => ['Flutter', 'React Native', 'Native integrations']],
            ['group' => 'Cloud & Deployment', 'tools' => ['AWS', 'DigitalOcean', 'CI/CD']],
            ['group' => 'Database Architecture', 'tools' => ['MySQL', 'PostgreSQL', 'Data modelling']],
            ['group' => 'Integrations & Automation', 'tools' => ['Paystack', 'Webhooks', 'Workflow systems']],
        ];

        $impacts = [
            ['index' => '01', 'title' => 'Faster digital operations', 'copy' => 'Less friction across the processes that keep work moving.'],
            ['index' => '02', 'title' => 'Stronger customer experiences', 'copy' => 'Clearer journeys that make digital interactions feel considered.'],
            ['index' => '03', 'title' => 'Scalable technical foundations', 'copy' => 'Architecture built to support change without unnecessary complexity.'],
            ['index' => '04', 'title' => 'Better business visibility', 'copy' => 'Connected information that supports confident decisions and control.'],
        ];

        $testimonials = [
            ['name' => 'Oluwafemi Oluwole', 'initials' => 'OO', 'company' => 'Oracode Limited', 'rating' => 96, 'quote' => 'Turance delivered a clean, scalable platform on time. Deployment was smooth and our team was impressed with the code quality.'],
            ['name' => 'Joy Ugoyah', 'initials' => 'JU', 'company' => 'Stavmia Technologies', 'rating' => 92, 'quote' => 'Their design and execution turned our idea into a product customers actually use. Communication was clear every step of the way.'],
            ['name' => 'Chirdan John', 'initials' => 'CJ', 'company' => 'Homafella Realty', 'rating' => 90, 'quote' => 'Professional and responsive. The new site improved lead quality and made our listings easier to manage.'],
            ['name' => 'Aquila Kalagbor', 'initials' => 'AK', 'company' => 'Aqqute Labs', 'rating' => 88, 'quote' => 'Attention to detail and practical solutions. They met our deadlines and kept the project on budget.'],
            ['name' => 'Excellence Idoko', 'initials' => 'EI', 'company' => 'MarketXtra Group', 'rating' => 94, 'quote' => 'Clear communication, measurable results, and a partner we can trust. The project exceeded our expectations.'],
            ['name' => 'Mafeng Micheal', 'initials' => 'MM', 'company' => 'BitesBanq Technologies', 'rating' => 91, 'quote' => 'They translated strategy into concrete features that boosted user engagement and simplified workflows.'],
            ['name' => 'Glory Atagholo', 'initials' => 'GA', 'company' => 'Vitasfield Agric Services', 'rating' => 89, 'quote' => 'Great collaboration and fast iterations. The launch went smoothly and adoption was quick.'],
            ['name' => 'Oghenetega Onadarho', 'initials' => 'OO', 'company' => 'IHcPro Integrated Service', 'rating' => 95, 'quote' => 'Reliable team, robust product, and excellent post-launch support. They stood by us through every update.'],
        ];

        $insights = [
            ['category' => 'Architecture', 'title' => 'Why scalable architecture matters before rapid growth', 'summary' => 'The early technical decisions that protect product momentum as demand changes.', 'date' => 'Editorial preview', 'time' => '6 min read', 'visual' => 'architecture', 'link' => null],
            ['category' => 'Product design', 'title' => 'Designing digital products around real user behaviour', 'summary' => 'How observation and evidence turn product assumptions into useful experiences.', 'date' => 'Editorial preview', 'time' => '5 min read', 'visual' => 'behaviour', 'link' => null],
            ['category' => 'Automation', 'title' => 'When business automation becomes a competitive advantage', 'summary' => 'Where thoughtful automation creates capacity without losing human judgement.', 'date' => 'Editorial preview', 'time' => '7 min read', 'visual' => 'automation', 'link' => null],
        ];

        $faqs = config('seo.home_faqs', []);
    @endphp

    <a class="tt-skip-link" href="#main-content">Skip to main content</a>

    <main class="tt-reference-home" id="main-content">
        <x-home.header />

        <section class="tt-hero" aria-labelledby="hero-title">
            <div class="tt-hero__curves" data-parallax-layer="curves" aria-hidden="true">
                <span class="tt-hero__glow" data-cursor-glow></span>
                <span class="tt-hero__sweep tt-hero__sweep--one"></span>
                <span class="tt-hero__sweep tt-hero__sweep--two"></span>
                <svg class="tt-hero__linework" viewBox="0 0 900 900" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="660" cy="426" rx="390" ry="318" stroke="currentColor" stroke-width="1" />
                </svg>
            </div>

            <canvas class="tt-hero__particles" data-particle-network aria-hidden="true"></canvas>

            <div class="tt-hero__main">
                <div class="tt-hero__content">
                    <div class="tt-hero__eyebrow tt-reveal tt-reveal--one">
                        <span>Web design & software development</span>
                        <i aria-hidden="true"></i>
                    </div>

                    <h1 class="tt-hero__title tt-reveal tt-reveal--two" id="hero-title"
                        aria-label="Excellence Delivered">
                        <span data-scramble aria-hidden="true">Excellence</span>
                        <span data-scramble aria-hidden="true">Delivered</span>
                    </h1>

                    <p class="tt-hero__description tt-reveal tt-reveal--three">
                        Conversion-focused websites, mobile apps and SaaS platforms<br>
                        for ambitious businesses in Nigeria and worldwide.
                    </p>

                    <div class="tt-hero__actions tt-reveal tt-reveal--four">
                        <x-home.primary-button :href="route('contact.show')"
                            data-conversion="home_hero_quote">
                            Get a project estimate
                        </x-home.primary-button>
                        <a class="tt-hero__secondary" href="{{ route('service.show') }}">
                            View services &amp; pricing
                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                        </a>
                    </div>

                    <ul class="tt-hero__assurances tt-reveal tt-reveal--four"
                        aria-label="What every Turance engagement includes">
                        <li>Strategy to launch</li>
                        <li>SEO-ready builds</li>
                        <li>Post-launch support</li>
                    </ul>
                </div>

                <x-home.hero-visual />
            </div>

            <div class="tt-service-highlights" aria-label="Core services">
                <x-home.service-highlight icon="strategy" title="Strategy"
                    subtitle="Purpose-driven solutions" />
                <x-home.service-highlight icon="design" title="Design"
                    subtitle="Intuitive and impactful" />
                <x-home.service-highlight icon="development" title="Development"
                    subtitle="Scalable and future-ready" />
            </div>
        </section>

        <section class="tt-section tt-about" id="about" aria-labelledby="about-title">
            <div class="tt-section__inner tt-about__grid">
                <div class="tt-about__content">
                    <x-home.section-heading eyebrow="About Turance" id="about-title"
                        title="Technology shaped around real business ambitions."
                        copy="Turance Technologies designs and develops digital products that help ambitious organisations operate better, connect with customers and scale with confidence." />

                    <ul class="tt-about__principles" aria-label="Our approach" data-reveal>
                        <li><span>01</span>Strategy before execution</li>
                        <li><span>02</span>Design with purpose</li>
                        <li><span>03</span>Technology built to scale</li>
                    </ul>
                </div>

                <div class="tt-about__visual" aria-hidden="true" data-reveal>
                    <svg width="0" height="0" style="position:absolute" focusable="false">
                        <filter id="tt-liquid-gold" x="-12%" y="-12%" width="124%" height="124%" color-interpolation-filters="sRGB">
                            <feTurbulence type="fractalNoise" baseFrequency="0.006 0.013" numOctaves="1" seed="7" result="tt-liquid-noise">
                                <animate attributeName="baseFrequency" dur="22s" values="0.006 0.013;0.008 0.017;0.005 0.01;0.006 0.013" repeatCount="indefinite" />
                            </feTurbulence>
                            <feDisplacementMap in="SourceGraphic" in2="tt-liquid-noise" scale="9" xChannelSelector="R" yChannelSelector="G" />
                        </filter>
                    </svg>
                    <span class="tt-about__orbit tt-about__orbit--outer"></span>
                    <span class="tt-about__orbit tt-about__orbit--inner"></span>
                    <span class="tt-about__node tt-about__node--one"></span>
                    <span class="tt-about__node tt-about__node--two"></span>
                    <span class="tt-about__node tt-about__node--three"></span>
                    <span class="tt-about__form"><img src="{{ asset('/assets/img/hero/turance-gold-sculpture.webp') }}" width="1254" height="1254" alt="" loading="lazy" decoding="async"></span>
                    <p><strong>One considered system.</strong><span>Strategy, design and engineering aligned around the outcome.</span></p>
                </div>
            </div>
        </section>

        <section class="tt-section tt-services" id="services" aria-labelledby="services-title">
            <div class="tt-section__inner tt-services__layout">
                <div class="tt-services__intro">
                    <x-home.section-heading eyebrow="What we do" id="services-title"
                        title="Expertise built for meaningful digital progress."
                        copy="Focused capabilities, brought together around the needs of each product." />
                    <a class="tt-text-link" href="{{ route('service.show') }}">Explore all capabilities
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                    </a>
                </div>

                <div class="tt-services__list">
                    @foreach ($services as $service)
                        <a class="tt-service-row" href="{{ route($service['route']) }}" data-reveal>
                            <span class="tt-service-row__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="tt-service-row__body">
                                <strong>{{ $service['name'] }}</strong>
                                <span>{{ $service['copy'] }}</span>
                            </span>
                            <span class="tt-service-row__arrow" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5" /></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-section tt-work" id="work" aria-labelledby="work-title">
            <div class="tt-section__inner">
                <x-home.section-heading eyebrow="Selected work" id="work-title"
                    title="Digital products created to move businesses forward."
                    copy="A selection of product platforms shaped around clear commercial and operational needs." />

                <div class="tt-work__list">
                    @foreach ($projects as $project)
                        <article class="tt-project {{ $loop->even ? 'tt-project--reverse' : '' }}" data-reveal>
                            <div class="tt-project__visual tt-project__visual--{{ $project['visual'] }}">
                                @if ($project['image'])
                                    <img src="{{ $project['image'] }}" alt="{{ $project['name'] }} interface preview" loading="lazy" decoding="async">
                                @else
                                    <div class="tt-project-ui" aria-label="Abstract interface preview for {{ $project['name'] }}" role="img">
                                        <div class="tt-project-ui__bar"><i></i><span>{{ $project['name'] }}</span><i></i></div>
                                        <div class="tt-project-ui__rail"><i></i><i></i><i></i><i></i></div>
                                        <div class="tt-project-ui__canvas">
                                            <span class="tt-project-ui__label">{{ $project['category'] }}</span>
                                            <strong>{{ $project['name'] }}</strong>
                                            <div class="tt-project-ui__metric"><i></i><i></i><i></i><i></i></div>
                                            <div class="tt-project-ui__panels"><i></i><i></i><i></i></div>
                                        </div>
                                    </div>
                                @endif
                                <span class="tt-project__index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <div class="tt-project__content">
                                <div class="tt-project__meta"><span>{{ $project['industry'] }}</span><span>{{ $project['year'] }}</span></div>
                                <h3>{{ $project['name'] }}</h3>
                                <p>{{ $project['summary'] }}</p>
                                <ul aria-label="Services provided">
                                    @foreach ($project['services'] as $service)
                                        <li>{{ $service }}</li>
                                    @endforeach
                                </ul>
                                <div class="tt-project__outcome"><span>Outcome</span><strong>{{ $project['outcome'] }}</strong></div>
                                @if ($project['link'])
                                    <a class="tt-text-link" href="{{ $project['link'] }}"
                                        @if (\Illuminate\Support\Str::startsWith($project['link'], ['http://', 'https://'])) target="_blank" rel="noopener noreferrer" @endif>
                                        {{ $project['link_label'] ?? 'View case study' }}
                                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                                    </a>
                                @else
                                    <span class="tt-project__status">Case study awaiting publication</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="tt-work__footer" data-reveal>
                    <p>Need to see work relevant to your brief?</p>
                    <a class="tt-text-link" href="{{ route('contact.show') }}">Request a tailored portfolio
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="tt-section tt-process" id="process" aria-labelledby="process-title">
            <div class="tt-section__inner">
                <x-home.section-heading eyebrow="How we work" id="process-title"
                    title="A clear process from ambition to execution."
                    copy="Every stage makes decisions visible, keeps momentum focused and protects the quality of the result." />

                <ol class="tt-process__timeline" data-process-timeline>
                    @foreach ($process as $stage)
                        <li data-reveal>
                            <span class="tt-process__node" aria-hidden="true"><i></i></span>
                            <span class="tt-process__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $stage['name'] }}</h3>
                            <p>{{ $stage['copy'] }}</p>
                        </li>
                    @endforeach
                    <span class="tt-process__progress" data-process-progress aria-hidden="true"></span>
                </ol>
            </div>
        </section>

        <section class="tt-section tt-capabilities" id="capabilities" aria-labelledby="capabilities-title">
            <div class="tt-section__inner">
                <x-home.section-heading eyebrow="Built with purpose" id="capabilities-title"
                    title="Modern technology selected around your product’s needs."
                    copy="Tools are chosen for fit, maintainability and the people who will rely on the product—not for trend value." />

                <div class="tt-capabilities__matrix">
                    @foreach ($capabilities as $capability)
                        <article data-reveal>
                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $capability['group'] }}</h3>
                            <ul>
                                @foreach ($capability['tools'] as $tool)
                                    <li>{{ $tool }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-section tt-impact" id="impact" aria-labelledby="impact-title">
            <div class="tt-impact__geometry" aria-hidden="true"><i></i><i></i><i></i><span><img src="{{ asset('/assets/img/hero/hero-bg-shape3.png') }}" width="250" height="250" alt="" loading="lazy" decoding="async"></span></div>
            <div class="tt-section__inner tt-impact__inner">
                <x-home.section-heading eyebrow="Beyond delivery" id="impact-title"
                    title="We build for progress you can measure."
                    copy="The product is only valuable when it improves how the organisation serves, decides and grows." />

                <div class="tt-impact__grid">
                    @foreach ($impacts as $impact)
                        <article data-reveal>
                            <span>{{ $impact['index'] }}</span>
                            <h3>{{ $impact['title'] }}</h3>
                            <p>{{ $impact['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-section tt-testimonials" id="perspectives" aria-labelledby="testimonials-title">
            <div class="tt-section__inner">
                <x-home.section-heading eyebrow="Client perspectives" id="testimonials-title"
                    title="Partnerships built on clarity and results."
                    copy="Past clients reflect on the quality, communication and dependable delivery behind their work with Turance." />

                <div class="tt-testimonial-carousel" data-testimonial-carousel data-reveal tabindex="0"
                    aria-roledescription="carousel" aria-label="Client testimonials. Use the left and right arrow keys to browse.">
                    <div class="tt-testimonial-carousel__viewport" aria-live="polite">
                        @foreach ($testimonials as $testimonial)
                            <article class="tt-testimonial-slide {{ $loop->first ? 'is-active' : '' }}"
                                data-testimonial-slide aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                                aria-label="Testimonial {{ $loop->iteration }} of {{ count($testimonials) }}">
                                <div class="tt-testimonial-slide__portrait">
                                    <span aria-hidden="true"></span>
                                    <div class="tt-testimonial-slide__initials"
                                        aria-label="{{ $testimonial['name'] }}">{{ $testimonial['initials'] }}</div>
                                </div>
                                <div class="tt-testimonial-slide__content">
                                    <div class="tt-testimonial-slide__topline">
                                        <span>Verified client perspective</span>
                                        <span class="tt-testimonial-rating" role="img"
                                            aria-label="{{ number_format($testimonial['rating'] / 20, 1) }} out of 5 stars">
                                            <i style="--rating: {{ $testimonial['rating'] }}%" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</i>
                                        </span>
                                    </div>
                                    <blockquote aria-label="&ldquo;{{ $testimonial['quote'] }}&rdquo;"><span data-decode aria-hidden="true">&ldquo;{{ $testimonial['quote'] }}&rdquo;</span></blockquote>
                                    <footer>
                                        <span></span>
                                        <div><strong aria-label="{{ $testimonial['name'] }}"><span data-decode aria-hidden="true">{{ $testimonial['name'] }}</span></strong><small>{{ $testimonial['company'] }}</small></div>
                                    </footer>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="tt-testimonial-carousel__controls">
                        <span class="tt-testimonial-carousel__counter"><b data-testimonial-current>01</b><i></i><span>{{ str_pad(count($testimonials), 2, '0', STR_PAD_LEFT) }}</span></span>
                        <div>
                            <button type="button" data-testimonial-previous aria-label="Show previous testimonial">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M10 7l-5 5 5 5" /></svg>
                            </button>
                            <button type="button" data-testimonial-next aria-label="Show next testimonial">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M14 7l5 5-5 5" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tt-section tt-insights" id="insights" aria-labelledby="insights-title">
            <div class="tt-section__inner">
                <x-home.section-heading eyebrow="Insights" id="insights-title"
                    title="Ideas for building stronger digital products."
                    copy="Practical thinking on product design, engineering decisions and digital operations." />

                <div class="tt-insights__grid">
                    @foreach ($insights as $insight)
                        <article class="tt-insight {{ $loop->first ? 'tt-insight--featured' : '' }}" data-reveal>
                            <div class="tt-insight__visual tt-insight__visual--{{ $insight['visual'] }}" aria-hidden="true">
                                <span></span><i></i><i></i><i></i>
                            </div>
                            <div class="tt-insight__body">
                                <div class="tt-insight__meta"><span>{{ $insight['category'] }}</span><span>{{ $insight['time'] }}</span></div>
                                <h3>{{ $insight['title'] }}</h3>
                                <p>{{ $insight['summary'] }}</p>
                                <div class="tt-insight__footer"><span>{{ $insight['date'] }}</span>
                                    @if ($insight['link'])
                                        <a href="{{ $insight['link'] }}" aria-label="Read {{ $insight['title'] }}"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg></a>
                                    @else
                                        <span class="tt-insight__soon">Coming soon</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="tt-section tt-faq" id="faq" aria-labelledby="faq-title">
            <div class="tt-section__inner tt-faq__grid">
                <div class="tt-faq__intro">
                    <x-home.section-heading eyebrow="Frequently asked questions" id="faq-title"
                        title="The details you may need before we begin."
                        copy="A concise view of how engagements are shaped. If your question is more specific, we are happy to discuss it." />
                    <a class="tt-text-link" href="{{ route('contact.show') }}">Ask another question
                        <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                    </a>
                </div>

                <div class="tt-faq__items" data-accordion>
                    @foreach ($faqs as $faq)
                        @php($faqId = 'faq-answer-'.$loop->iteration)
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

        <section class="tt-contact-cta" id="contact" aria-labelledby="contact-title">
            <div class="tt-contact-cta__network" aria-hidden="true"><i></i><i></i><i></i><i></i><span><img src="{{ asset('/assets/img/hero/hero-bg-shape3.png') }}" width="250" height="250" alt="" loading="lazy" decoding="async"></span></div>
            <div class="tt-section__inner tt-contact-cta__inner">
                <x-home.section-heading theme="dark" eyebrow="Start a conversation" id="contact-title"
                    title="Have an ambitious digital idea? Let’s shape it properly."
                    copy="Tell us what you are building, improving or trying to solve. We will help you identify the right next step." />
                <div class="tt-contact-cta__actions" data-reveal>
                    <x-home.primary-button :href="route('contact.show')"
                        data-conversion="home_bottom_quote">Get a project estimate</x-home.primary-button>
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
    <script src="{{ asset('/assets/js/home-reference.js') }}?v=2.5" defer></script>
@endpush
