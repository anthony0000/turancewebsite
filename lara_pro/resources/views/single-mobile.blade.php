@extends('layouts.master')

@section('content')
    <main>
        <section class="wt-breadcrumb-area pt-215 pb-80">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-xl-12">
                        <div class="wt-breadcrumb-wrapper single-service">
                            <div class="wt-breadcrumb-content">
                                <h1 class="wt-breadcrumb-title">mobile app development</h1>
                                <p class="wt-breadcrumb-paragraph">
                                    Mobile is where convenience becomes loyalty. We design and develop apps that feel
                                    effortless from the first tap, keep users engaged over time, and help businesses
                                    create stronger everyday relationships with their audience. Whether you are launching a
                                    customer app, an internal operations tool, a marketplace, or a subscription-based
                                    product, we blend product strategy, clean UX, and reliable engineering to build apps
                                    people actually enjoy using.
                                </p>
                            </div>
                            <div class="wt-breadcrumb-list">
                                <ul>
                                    <li><span><i class="flaticon-check-mark"></i></span> User-centered app experiences
                                        designed for retention, trust, and smooth onboarding</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> Native and cross-platform
                                        development matched to your budget, timeline, and growth goals</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> Secure APIs, payments, push
                                        notifications, analytics, and third-party integrations from day one</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> App store launch support and
                                        ongoing product improvement after release</li>
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
                                <img src="{{ asset('assets/img/service/single1.jpg') }}"
                                    alt="Mobile app development showcase">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3"></div>
                    <div class="col-xl-7">
                        <div class="wt-service-single-details-content mb-75 wt_fade_anim" data-delay=".4">
                            <h2 class="wt-service-single-details-title wt-char-animation">Apps that feel simple to use,
                                fast to trust, and hard to abandon</h2>
                            <p class="wt-service-single-details-paragraph">The best mobile products remove friction. They
                                guide new users naturally, make repeat actions effortless, and create a consistent sense
                                of reliability. We focus on the details that drive adoption, from intuitive navigation and
                                strong onboarding to performance tuning, real-time interactions, and thoughtful feature
                                prioritization.</p>
                            <p class="wt-service-single-details-paragraph">Behind the interface, we build systems that
                                support growth. That includes secure backends, scalable APIs, user roles, payment flows,
                                analytics, notifications, and dashboards where needed. The result is not just an app that
                                looks modern, but one that supports real business operations and long-term product
                                success.</p>
                        </div>
                    </div>
                    <div class="col-xl-3"></div>
                    <div class="col-xl-7 col-lg-10">
                        <div class="wt-service-single-details-wrap mb-65">
                            <div class="wt-service-single-details-content mb-45 wt_fade_anim">
                                <h2 class="wt-service-single-details-title wt-char-animation">How we move your app from
                                    concept to launch-ready product</h2>
                                <p class="wt-service-single-details-paragraph">Every strong mobile product starts with
                                    clarity. We keep the process focused on real user value, sharp execution, and the
                                    features that matter most for launch and growth.</p>
                            </div>
                            <div class="wt-faq-accordion accordion-flush wt_fade_anim" data-delay=".5"
                                id="accordionMobileProcess">
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-process-one"
                                            aria-expanded="false" aria-controls="mobile-process-one">
                                            <span class="number">01.</span>
                                            Product discovery and feature planning
                                        </button>
                                    </h2>
                                    <div id="mobile-process-one" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionMobileProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">We define your audience, product goal,
                                                MVP scope, and core user flows. This stage helps us avoid bloated
                                                roadmaps and focus on the features that create the quickest path to value.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-process-two"
                                            aria-expanded="false" aria-controls="mobile-process-two">
                                            <span class="number">02.</span>
                                            UX design and interactive prototyping
                                        </button>
                                    </h2>
                                    <div id="mobile-process-two" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionMobileProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Next, we design the experience screen by
                                                screen. We map flows, create wireframes, refine the interface, and test
                                                the product logic through prototypes before development begins.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-process-three"
                                            aria-expanded="false" aria-controls="mobile-process-three">
                                            <span class="number">03.</span>
                                            Engineering, backend and integrations
                                        </button>
                                    </h2>
                                    <div id="mobile-process-three" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionMobileProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">We build the app using the right stack
                                                for your product, whether native or cross-platform. We also handle API
                                                integrations, payment systems, notifications, admin tools, and data flows
                                                needed to make the product operational.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-process-four"
                                            aria-expanded="false" aria-controls="mobile-process-four">
                                            <span class="number">04.</span>
                                            Testing, launch and iteration
                                        </button>
                                    </h2>
                                    <div id="mobile-process-four" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionMobileProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Before release, we run device testing,
                                                performance checks, QA reviews, and store-readiness validation. After
                                                launch, we can keep improving the product with updates, user feedback, and
                                                usage insights.</p>
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
                                    alt="Mobile product design presentation">
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3"></div>
                    <div class="col-xl-7 col-lg-10">
                        <div class="wt-service-single-details-wrap mb-65">
                            <div class="wt-service-single-details-content mb-45 wt_fade_anim">
                                <h2 class="wt-service-single-details-title wt-char-animation">What clients usually want
                                    to know before building an app</h2>
                                <p class="wt-service-single-details-paragraph">Mobile development is a big investment, so
                                    the questions matter. These are some of the conversations we typically have before
                                    the first screen is designed.</p>
                            </div>
                            <div class="wt-faq-accordion accordion-flush wt_fade_anim" data-delay=".5"
                                id="accordionMobileFaq">
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-faq-one"
                                            aria-expanded="false" aria-controls="mobile-faq-one">
                                            <span class="number">01.</span>
                                            Should we build native or cross-platform?
                                        </button>
                                    </h2>
                                    <div id="mobile-faq-one" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionMobileFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">That depends on your product priorities.
                                                If speed to market and shared development matter most, cross-platform is
                                                often a smart option. If deep platform-specific performance is critical,
                                                native may be the better fit. We help you choose based on the real use
                                                case, not trends.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-faq-two"
                                            aria-expanded="false" aria-controls="mobile-faq-two">
                                            <span class="number">02.</span>
                                            Can you build the backend and admin dashboard too?
                                        </button>
                                    </h2>
                                    <div id="mobile-faq-two" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionMobileFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Yes. We can build the backend services,
                                                APIs, databases, dashboards, and integrations required to power the app
                                                properly, so you are not left with a beautiful interface and nowhere for
                                                the product logic to live.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-faq-three"
                                            aria-expanded="false" aria-controls="mobile-faq-three">
                                            <span class="number">03.</span>
                                            How long does it take to launch an MVP?
                                        </button>
                                    </h2>
                                    <div id="mobile-faq-three" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionMobileFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">A focused MVP can move quickly when the
                                                scope is clear and the must-have features are prioritized well. Once we
                                                define the product scope, we provide a practical delivery plan with clear
                                                milestones and launch expectations.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#mobile-faq-four"
                                            aria-expanded="false" aria-controls="mobile-faq-four">
                                            <span class="number">04.</span>
                                            Do you support updates after launch?
                                        </button>
                                    </h2>
                                    <div id="mobile-faq-four" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionMobileFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Yes. We can stay involved after launch
                                                to handle version updates, performance improvements, bug fixes, feature
                                                additions, analytics reviews, and the product changes that naturally come
                                                with growth.</p>
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
                                        alt="SaaS dashboards and portals">
                                </div>
                                <div class="wt-service-single-details-button-cnt">
                                    <h4>SaaS Dashboards & Portals</h4>
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
                                    <h4>App Branding & Growth Assets</h4>
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
                                        alt="Brand identity for digital products">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
