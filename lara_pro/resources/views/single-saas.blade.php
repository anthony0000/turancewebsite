@extends('layouts.master')

@section('content')
    <main>
        <section class="wt-breadcrumb-area pt-215 pb-80">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-xl-12">
                        <div class="wt-breadcrumb-wrapper single-service">
                            <div class="wt-breadcrumb-content">
                                <h1 class="wt-breadcrumb-title">saas product design and development</h1>
                                <p class="wt-breadcrumb-paragraph">
                                    Great SaaS products do not win by piling on features. They win by helping users reach
                                    value quickly, reducing friction in complex workflows, and making growth feel
                                    natural. We design and build SaaS platforms that balance usability, performance, and
                                    scalability so your product feels polished to customers and dependable to your team.
                                </p>
                            </div>
                            <div class="wt-breadcrumb-list">
                                <ul>
                                    <li><span><i class="flaticon-check-mark"></i></span> Product strategy focused on
                                        onboarding, activation, retention, and expansion</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> Clean dashboards and workflows
                                        that simplify complex tasks for real users</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> Architecture for subscriptions,
                                        permissions, analytics, and third-party integrations</li>
                                    <li><span><i class="flaticon-check-mark"></i></span> Built to launch lean, grow fast,
                                        and stay maintainable as your product evolves</li>
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
                                    alt="SaaS product design and development showcase">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3"></div>
                    <div class="col-xl-7">
                        <div class="wt-service-single-details-content mb-75 wt_fade_anim" data-delay=".4">
                            <h2 class="wt-service-single-details-title wt-char-animation">SaaS platforms built for
                                activation, retention, and revenue growth</h2>
                            <p class="wt-service-single-details-paragraph">SaaS users expect clarity. They want to sign
                                up quickly, understand the product without friction, and complete their work with
                                confidence. We focus on the experience details that reduce confusion and help users reach
                                their first meaningful success as early as possible.</p>
                            <p class="wt-service-single-details-paragraph">At the same time, strong SaaS products need
                                solid foundations. We design and build the dashboards, billing flows, account management,
                                reporting views, permission systems, and integrations required to support a product that
                                can grow with your customer base and internal operations. The goal is simple: a platform
                                that feels easy to use and is ready to scale.</p>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="wt-service-single-details-thumb wt_fade_anim" data-delay=".3">
                            <img src="{{ asset('assets/img/service/service-single2.jpg') }}"
                                alt="SaaS dashboard user experience">
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="wt-service-single-details-thumb wt_fade_anim" data-delay=".5">
                            <img src="{{ asset('assets/img/service/service-single3.jpg') }}"
                                alt="SaaS platform interface design">
                        </div>
                    </div>
                    <div class="col-xl-3"></div>
                    <div class="col-xl-7 col-lg-10">
                        <div class="wt-service-single-details-wrap mb-65">
                            <div class="wt-service-single-details-content mb-45 wt_fade_anim">
                                <h2 class="wt-service-single-details-title wt-char-animation">How we shape SaaS products
                                    from idea to launch-ready platform</h2>
                                <p class="wt-service-single-details-paragraph">We work from product thinking first. That
                                    means we are not just designing screens or shipping features. We are building a SaaS
                                    experience that helps users understand, adopt, and keep paying for the product.</p>
                            </div>
                            <div class="wt-faq-accordion accordion-flush wt_fade_anim" data-delay=".5"
                                id="accordionSaasProcess">
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-process-one"
                                            aria-expanded="false" aria-controls="saas-process-one">
                                            <span class="number">01.</span>
                                            Product discovery and roadmap definition
                                        </button>
                                    </h2>
                                    <div id="saas-process-one" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionSaasProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">We clarify the product vision, target
                                                user, problem space, core jobs to be done, and MVP priorities. This stage
                                                gives the product a sharper roadmap and keeps the first release focused on
                                                real value.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-process-two"
                                            aria-expanded="false" aria-controls="saas-process-two">
                                            <span class="number">02.</span>
                                            UX systems and workflow design
                                        </button>
                                    </h2>
                                    <div id="saas-process-two" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionSaasProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">We design the information architecture,
                                                navigation, dashboards, empty states, onboarding, and key workflows so the
                                                product feels coherent from first login to daily use.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-process-three"
                                            aria-expanded="false" aria-controls="saas-process-three">
                                            <span class="number">03.</span>
                                            SaaS engineering and integrations
                                        </button>
                                    </h2>
                                    <div id="saas-process-three" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionSaasProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">We build the product foundations needed
                                                for growth, including authentication, subscriptions, billing, user roles,
                                                reporting, notifications, APIs, admin tools, and third-party service
                                                integrations.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-process-four"
                                            aria-expanded="false" aria-controls="saas-process-four">
                                            <span class="number">04.</span>
                                            Launch, measurement and iteration
                                        </button>
                                    </h2>
                                    <div id="saas-process-four" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionSaasProcess">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">After launch, we help monitor adoption,
                                                identify friction points, and improve the experience using user feedback,
                                                product analytics, and roadmap priorities. SaaS growth is iterative, and
                                                the product should evolve with the market.</p>
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
                                    alt="SaaS product growth and analytics">
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3"></div>
                    <div class="col-xl-7 col-lg-10">
                        <div class="wt-service-single-details-wrap mb-65">
                            <div class="wt-service-single-details-content mb-45 wt_fade_anim">
                                <h2 class="wt-service-single-details-title wt-char-animation">Common SaaS questions we
                                    answer before building</h2>
                                <p class="wt-service-single-details-paragraph">SaaS products touch strategy, design,
                                    technology, and business model decisions at once. These are some of the common
                                    questions we help founders and teams work through early.</p>
                            </div>
                            <div class="wt-faq-accordion accordion-flush wt_fade_anim" data-delay=".5"
                                id="accordionSaasFaq">
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-faq-one"
                                            aria-expanded="false" aria-controls="saas-faq-one">
                                            <span class="number">01.</span>
                                            Can you build an MVP first and scale it later?
                                        </button>
                                    </h2>
                                    <div id="saas-faq-one" class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionSaasFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Yes. In fact, that is often the smartest
                                                approach. We help define the smallest valuable version of the product,
                                                build it with future growth in mind, and leave room for additional modules
                                                and improvements after market feedback.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-faq-two"
                                            aria-expanded="false" aria-controls="saas-faq-two">
                                            <span class="number">02.</span>
                                            Can the platform support subscriptions, teams, and permissions?
                                        </button>
                                    </h2>
                                    <div id="saas-faq-two" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionSaasFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Yes. We can architect for recurring
                                                billing, plan tiers, user roles, team spaces, account permissions, and the
                                                administrative controls required for a serious SaaS platform.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-faq-three"
                                            aria-expanded="false" aria-controls="saas-faq-three">
                                            <span class="number">03.</span>
                                            Do you help with admin dashboards and reporting?
                                        </button>
                                    </h2>
                                    <div id="saas-faq-three" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionSaasFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Absolutely. Internal dashboards,
                                                reporting views, moderation tools, support panels, and operational screens
                                                are often essential to making the product manageable for your team as it
                                                grows.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="wt-faq-accordion-item accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="wt-faq-accordion-title accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#saas-faq-four"
                                            aria-expanded="false" aria-controls="saas-faq-four">
                                            <span class="number">04.</span>
                                            Will you support ongoing product improvements after launch?
                                        </button>
                                    </h2>
                                    <div id="saas-faq-four" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionSaasFaq">
                                        <div class="wt-faq-accordion-descri accordion-body">
                                            <p class="wt-faq-accordion-paragraph">Yes. Ongoing product work is usually a
                                                core part of SaaS success. We can continue with feature iterations, UX
                                                refinements, integration work, performance improvements, and roadmap
                                                support based on user behavior and business goals.</p>
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
                                        alt="Website design and development">
                                </div>
                                <div class="wt-service-single-details-button-cnt">
                                    <h4>Conversion Websites</h4>
                                    <div class="wt-service-single-details-btn icon_main">
                                        <a class="wt-header-btn" href="{{ route('services.web') }}">Read More
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
                                    <h4>Mobile Companion Apps</h4>
                                    <div class="wt-service-single-details-btn icon_main">
                                        <a class="wt-header-btn" href="{{ route('services.mobile') }}">Read More
                                            <span class="icon_box">
                                                <i class="icon_first fa-regular fa-arrow-right"></i>
                                                <i class="icon_second fa-regular fa-arrow-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="wt-service-single-details-button-img">
                                    <img src="{{ asset('assets/img/service/service-single6.jpg') }}"
                                        alt="Mobile app product extension">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
