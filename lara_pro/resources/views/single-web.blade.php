@extends('layouts.master')

@section('content')
    <main>
        <section class="wt-breadcrumb-area pt-215 pb-80">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-xl-12">
                        <div class="wt-breadcrumb-wrapper single-service">
                            <div class="wt-breadcrumb-content">
                                <h1 class="wt-breadcrumb-title">website design and development</h1>
                                <p class="wt-breadcrumb-paragraph">
                                    Your website is often the first serious conversation your brand has with a potential
                                    client. We design and build premium websites that help you look credible, communicate
                                    value clearly, and turn attention into qualified enquiries. From corporate websites
                                    and landing pages to custom platforms and marketing sites, we combine strategy,
                                    persuasive design, and clean engineering to create a digital presence that works even
                                    while you sleep.
                                </p>
                            </div>
                            <div class="wt-breadcrumb-list">
                                <ul>
                                    <li><span><i class="flaticon-check-mark"></i></span> Strategy-led websites that
                                        position your business with clarity and confidence</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> Conversion-focused UX, content
                                        flow, and calls to action that guide visitors to take the next step</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> Fast, responsive development
                                        with SEO-ready structure and scalable architecture</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> CMS, analytics, and
                                        integrations that support long-term growth after launch</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="wt-service-single-details-area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="wt-service-single-details-wrapper">
                            <div class="wt-service-single-details-thumb">
                                <img src="{{ asset('assets/img/service/single2.jpg') }}"
                                    alt="Website design and development showcase">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3"></div>
                    <div class="col-xl-7">
                        <div class="wt-service-single-details-content mb-75 wt_fade_anim" data-delay=".4">
                            <h2 class="wt-service-single-details-title wt-char-animation">Web experiences that build
                                trust fast and turn visits into action</h2>
                            <p class="wt-service-single-details-paragraph">A good-looking website is not enough. The
                                real goal is to help your audience understand who you are, why you are different, and
                                what they should do next. That is why we shape every website around strong positioning,
                                clean user journeys, persuasive page structure, and seamless interactions that feel
                                premium from the first scroll.</p>
                            <p class="wt-service-single-details-paragraph">Whether you need a lead-generation website,
                                a polished company profile, a campaign landing page, or a custom business platform, we
                                build with performance and flexibility in mind. Your site will be ready to scale with
                                content updates, marketing campaigns, new features, and the level of trust modern
                                clients expect before they ever send a message.</p>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="wt-service-single-details-thumb wt_fade_anim" data-delay=".3">
                            <img src="{{ asset('assets/img/service/service-single2.jpg') }}"
                                alt="Website user interface design detail">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="wt-service-single-details-thumb wt_fade_anim" data-delay=".5">
                            <img src="{{ asset('assets/img/service/service-single3.jpg') }}"
                                alt="Responsive website development presentation">
                        </div>
                    </div>
                    <div class="col-xl-3"></div>
                    <div class="col-xl-7 col-lg-10">
                        <div class="wt-service-single-details-wrap mb-65">
                            <div class="wt-service-single-details-content mb-45 wt_fade_anim">
                                <h2 class="wt-service-single-details-title wt-char-animation">How we build websites that
                                    win trust and generate leads</h2>
                                <p class="wt-service-single-details-paragraph">Our process keeps the creative work sharp
                                    and the business goals clear. Every decision is built around helping your website
                                    communicate better, convert better, and stay useful as your company grows.</p>
                            </div>
                            <div class="wt-faq-accordion accordion-flush wt_fade_anim" data-delay=".5"
                                id="accordionWebsiteProcess">
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-process-one"
                                            aria-expanded="false" aria-controls="website-process-one">
                                            <span class="number">01.</span>
                                            Discovery and positioning
                                        </button>
                                    </h2>
                                    <div id="website-process-one" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionWebsiteProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">We start by understanding your offer,
                                                target audience, competitive landscape, and business goals. This helps us
                                                define the right structure, messaging direction, and user journey before
                                                design begins.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-process-two"
                                            aria-expanded="false" aria-controls="website-process-two">
                                            <span class="number">02.</span>
                                            UX architecture and content planning
                                        </button>
                                    </h2>
                                    <div id="website-process-two" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionWebsiteProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Next, we map page hierarchy, key user
                                                flows, wireframes, and conversion paths. This stage ensures the website
                                                feels intuitive, communicates in the right order, and supports both
                                                storytelling and action.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-process-three"
                                            aria-expanded="false" aria-controls="website-process-three">
                                            <span class="number">03.</span>
                                            Visual design and development
                                        </button>
                                    </h2>
                                    <div id="website-process-three" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionWebsiteProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">We translate the strategy into a
                                                polished interface backed by clean, responsive development. Typography,
                                                spacing, imagery, animation, forms, and interactions all work together to
                                                make the experience feel modern and trustworthy.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-process-four"
                                            aria-expanded="false" aria-controls="website-process-four">
                                            <span class="number">04.</span>
                                            Launch, optimization and support
                                        </button>
                                    </h2>
                                    <div id="website-process-four" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionWebsiteProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Before launch, we test for performance,
                                                responsiveness, accessibility, and usability. After go-live, we can
                                                support updates, analytics reviews, SEO improvements, and future feature
                                                rollouts so the website keeps delivering value.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="wt-service-single-details-wrapper wt_fade_anim">
                            <div class="wt-service-single-details-thumb">
                                <img src="{{ asset('assets/img/service/service-single4.jpg') }}"
                                    alt="Website development hero showcase">
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3"></div>
                    <div class="col-xl-7 col-lg-10">
                        <div class="wt-service-single-details-wrap mb-65">
                            <div class="wt-service-single-details-content mb-45 wt_fade_anim">
                                <h2 class="wt-service-single-details-title wt-char-animation">Questions clients usually
                                    ask before starting a website project</h2>
                                <p class="wt-service-single-details-paragraph">The best websites feel simple to the user,
                                    but there is a lot of smart thinking behind them. Here are some of the questions we
                                    answer most often before we begin.</p>
                            </div>
                            <div class="wt-faq-accordion accordion-flush wt_fade_anim" data-delay=".5"
                                id="accordionWebsiteFaq">
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-faq-one"
                                            aria-expanded="false" aria-controls="website-faq-one">
                                            <span class="number">01.</span>
                                            How long does a professional website take?
                                        </button>
                                    </h2>
                                    <div id="website-faq-one" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionWebsiteFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">A focused landing page or company site
                                                can move quickly, while larger websites with custom functionality take
                                                longer. After our discovery phase, we give you a clear timeline with key
                                                milestones so you know exactly what to expect.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-faq-two"
                                            aria-expanded="false" aria-controls="website-faq-two">
                                            <span class="number">02.</span>
                                            Can you redesign an existing website without losing everything?
                                        </button>
                                    </h2>
                                    <div id="website-faq-two" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionWebsiteFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Yes. We can refresh your visual design,
                                                improve messaging, modernize the user experience, and preserve valuable
                                                content or SEO foundations where it makes sense. The goal is improvement,
                                                not disruption.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-faq-three"
                                            aria-expanded="false" aria-controls="website-faq-three">
                                            <span class="number">03.</span>
                                            Will my team be able to manage the website after launch?
                                        </button>
                                    </h2>
                                    <div id="website-faq-three" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionWebsiteFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Absolutely. We can integrate a content
                                                management system and structure the backend so your team can update pages,
                                                blog posts, case studies, or product content without depending on a
                                                developer for every change.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#website-faq-four"
                                            aria-expanded="false" aria-controls="website-faq-four">
                                            <span class="number">04.</span>
                                            Do you support performance, SEO and future updates?
                                        </button>
                                    </h2>
                                    <div id="website-faq-four" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionWebsiteFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Yes. We build with speed, structure, and
                                                discoverability in mind from the start, and we can continue supporting
                                                your site with ongoing optimization, maintenance, analytics, and feature
                                                enhancements after launch.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <div class="wt-service-single-details-nav">
                            <div class="wt-service-single-details-button wt_fade_anim" data-delay=".3">
                                <div class="wt-service-single-details-button-img">
                                    <img src="{{ asset('assets/img/service/service-single5.jpg') }}"
                                        alt="SaaS product design and development">
                                </div>
                                <div class="wt-service-single-details-button-cnt">
                                    <h4>SaaS Platforms</h4>
                                    <div class="wt-service-single-details-btn icon_main">
                                        <a class="wt-header-btn" href="{{ route('services.saas') }}">Read More
                                            <span class="icon_box">
                                                <i class="icon_first fa-regular fa-arrow-right"></i>
                                                <i class="icon_second fa-regular fa-arrow-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="wt-service-single-details-button wt_fade_anim" data-delay=".5">
                                <div class="wt-service-single-details-button-cnt">
                                    <h4>Brand Strategy & Identity</h4>
                                    <div class="wt-service-single-details-btn icon_main">
                                        <a class="wt-header-btn" href="{{ route('services.branding') }}">Read More
                                            <span class="icon_box">
                                                <i class="icon_first fa-regular fa-arrow-right"></i>
                                                <i class="icon_second fa-regular fa-arrow-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="wt-service-single-details-button-img">
                                    <img src="{{ asset('assets/img/service/service-single6.jpg') }}"
                                        alt="Branding and identity design">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
