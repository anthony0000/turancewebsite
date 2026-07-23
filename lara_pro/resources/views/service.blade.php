@extends('layouts.master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/assets/css/service-luxury.css') }}">
@endpush

@section('content')
    <main class="tt-service-page">
        <section class="tt-service-hero pt-215 pb-120">
            <div class="tt-service-hero-noise"></div>
            <div class="tt-service-hero-halo tt-service-hero-halo-one"></div>
            <div class="tt-service-hero-halo tt-service-hero-halo-two"></div>
            <div class="container">
                <div class="row align-items-center gy-5">
                    <div class="col-xl-7 col-lg-7">
                        <div class="tt-service-hero-copy">
                            <span class="tt-service-kicker tt-service-reveal">Premier Digital Craft</span>
                            <h1 class="tt-service-title wt-char-animation">Web, mobile, SaaS, and branding built to feel
                                premium from the first glance to the final click.</h1>
                            <p class="tt-service-lead tt-service-reveal">
                                Turance Technologies blends strategy, design, engineering, and brand thinking into one
                                seamless delivery process. The result is a sharper market presence, stronger client
                                confidence, and digital experiences that look as valuable as the business behind them.
                            </p>
                            <div class="tt-service-actions tt-service-reveal">
                                <a class="tt-service-btn tt-service-btn-primary wt-btn-bounce"
                                    href="{{ route('contact.show') }}">Start your project</a>
                                <a class="tt-service-btn tt-service-btn-secondary" href="#service-pricing">See pricing</a>
                            </div>
                            <div class="tt-service-stat-grid">
                                <div class="tt-service-stat-card tt-service-reveal">
                                    <span class="tt-service-stat-value">
                                        <span data-service-count="4" data-service-suffix="+">0</span>
                                    </span>
                                    <p>premium service pillars delivered under one team</p>
                                </div>
                                <div class="tt-service-stat-card tt-service-reveal">
                                    <span class="tt-service-stat-value">
                                        <span data-service-count="3">0</span>
                                    </span>
                                    <p>engagement tiers built for lean launches to flagship builds</p>
                                </div>
                                <div class="tt-service-stat-card tt-service-reveal">
                                    <span class="tt-service-stat-value">
                                        <span data-service-count="1">0</span>
                                    </span>
                                    <p>clear delivery partner from discovery, design, build, and launch</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-5">
                        <div class="tt-service-hero-stage">
                            <div class="tt-service-orbit">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <div class="tt-service-hero-frame tt-service-tilt-card">
                                <img src="{{ asset('assets/img/service/service-inner-hero-thumb.jpg') }}"
                                    alt="Premium digital services presentation">
                                <div class="tt-service-frame-badge">
                                    <span>Signature Delivery</span>
                                    <strong>Strategy, design, build, launch</strong>
                                </div>
                            </div>
                            <div class="tt-service-floating-card one">
                                <span>Luxury web presence</span>
                                <strong>Authority-led websites</strong>
                                <p>Positioning, UX, motion, and conversion structure in one experience.</p>
                            </div>
                            <div class="tt-service-floating-card two">
                                <span>Product execution</span>
                                <strong>Mobile and SaaS systems</strong>
                                <p>Built for serious usability, scalability, and polished market fit.</p>
                            </div>
                            <div class="tt-service-floating-card three">
                                <span>Brand excellence</span>
                                <strong>Identity that feels expensive</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tt-service-marquee">
            <div class="tt-service-marquee-track">
                <span>Positioning</span>
                <span>User Experience</span>
                <span>Conversion Design</span>
                <span>Mobile Product Strategy</span>
                <span>SaaS Architecture</span>
                <span>Motion Direction</span>
                <span>Brand Systems</span>
                <span>Launch Support</span>
                <span>Positioning</span>
                <span>User Experience</span>
                <span>Conversion Design</span>
                <span>Mobile Product Strategy</span>
                <span>SaaS Architecture</span>
                <span>Motion Direction</span>
                <span>Brand Systems</span>
                <span>Launch Support</span>
            </div>
        </section>

        <section class="tt-service-offerings pt-120 pb-120">
            <div class="container">
                <div class="row align-items-end mb-50">
                    <div class="col-xl-7 col-lg-8">
                        <div class="tt-service-section-heading">
                            <span class="tt-service-kicker">Signature Offerings</span>
                            <h2 class="tt-service-section-title wt-char-animation">Services shaped for brands that need
                                more than a nice-looking interface.</h2>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-4">
                        <p class="tt-service-section-copy tt-service-reveal">
                            Every offering is designed to elevate perception, simplify the user journey, and create
                            measurable business momentum, whether you need a sharper online presence or a full digital
                            product ecosystem.
                        </p>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-xl-6 col-lg-6">
                        <a class="tt-service-offer-card tt-service-reveal" href="{{ route('services.web') }}">
                            <span class="tt-service-offer-index">01</span>
                            <div class="tt-service-offer-head">
                                <h3>Website Experiences</h3>
                                <span class="tt-service-offer-price">From $1.5k</span>
                            </div>
                            <p>For companies that need their digital presence to communicate confidence, clarity, and
                                value instantly.</p>
                            <ul>
                                <li>Brand-led structure and messaging</li>
                                <li>High-conversion UX and responsive development</li>
                                <li>SEO-ready architecture and content flow</li>
                            </ul>
                            <span class="tt-service-offer-cta">Explore web service <i
                                    class="fa-regular fa-arrow-up-right"></i></span>
                        </a>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <a class="tt-service-offer-card tt-service-reveal" href="{{ route('services.mobile') }}">
                            <span class="tt-service-offer-index">02</span>
                            <div class="tt-service-offer-head">
                                <h3>Mobile Products</h3>
                                <span class="tt-service-offer-price">From $6k</span>
                            </div>
                            <p>For ambitious teams building customer apps, internal tools, or platforms that must feel
                                effortless from the first tap.</p>
                            <ul>
                                <li>Product strategy, flows, and screen design</li>
                                <li>Cross-platform or native build paths</li>
                                <li>Backend, payments, notifications, and analytics</li>
                            </ul>
                            <span class="tt-service-offer-cta">Explore mobile service <i
                                    class="fa-regular fa-arrow-up-right"></i></span>
                        </a>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <a class="tt-service-offer-card tt-service-reveal" href="{{ route('services.saas') }}">
                            <span class="tt-service-offer-index">03</span>
                            <div class="tt-service-offer-head">
                                <h3>SaaS Platforms</h3>
                                <span class="tt-service-offer-price">From $9k</span>
                            </div>
                            <p>For founders and operators who need scalable product systems, clean onboarding, and a
                                polished platform experience.</p>
                            <ul>
                                <li>MVP scope definition and product architecture</li>
                                <li>Dashboards, user roles, and workflow design</li>
                                <li>Integrations, billing, and scalable UI systems</li>
                            </ul>
                            <span class="tt-service-offer-cta">Explore SaaS service <i
                                    class="fa-regular fa-arrow-up-right"></i></span>
                        </a>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <a class="tt-service-offer-card tt-service-reveal" href="{{ route('services.branding') }}">
                            <span class="tt-service-offer-index">04</span>
                            <div class="tt-service-offer-head">
                                <h3>Branding Systems</h3>
                                <span class="tt-service-offer-price">From $1.5k</span>
                            </div>
                            <p>For businesses ready to look premium, sound more distinct, and present themselves with
                                consistency across every touchpoint.</p>
                            <ul>
                                <li>Logo direction, typography, and color systems</li>
                                <li>Premium brand application guidelines</li>
                                <li>Visual assets ready for digital growth</li>
                            </ul>
                            <span class="tt-service-offer-cta">Explore branding service <i
                                    class="fa-regular fa-arrow-up-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="tt-service-difference pb-120">
            <div class="container">
                <div class="tt-service-panel">
                    <div class="row align-items-center g-4">
                        <div class="col-xl-5">
                            <div class="tt-service-section-heading">
                                <span class="tt-service-kicker">Why It Feels Different</span>
                                <h2 class="tt-service-section-title wt-char-animation">Luxury is not decoration. It is
                                    clarity, restraint, and disciplined execution.</h2>
                            </div>
                        </div>
                        <div class="col-xl-7">
                            <div class="tt-service-feature-grid">
                                <div class="tt-service-feature-card tt-service-reveal">
                                    <span>01</span>
                                    <h3>Strategic before visual</h3>
                                    <p>We do not jump straight to screens. We clarify the message, audience, offer, and
                                        user flow first, so the final experience feels expensive because it makes sense.
                                    </p>
                                </div>
                                <div class="tt-service-feature-card tt-service-reveal">
                                    <span>02</span>
                                    <h3>Design that sells quietly</h3>
                                    <p>Premium design does not shout. It guides, reassures, and creates trust through
                                        balance, hierarchy, motion, and detail.</p>
                                </div>
                                <div class="tt-service-feature-card tt-service-reveal">
                                    <span>03</span>
                                    <h3>Engineering with longevity</h3>
                                    <p>We build digital systems that can grow with marketing, operations, integrations,
                                        and future features instead of trapping you in rework.</p>
                                </div>
                                <div class="tt-service-feature-card tt-service-reveal">
                                    <span>04</span>
                                    <h3>Refinement after launch</h3>
                                    <p>We care about how the experience performs once real users arrive, which is where
                                        the polish often separates average from exceptional.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tt-service-process pb-120">
            <div class="container">
                <div class="row g-5">
                    <div class="col-xl-5">
                        <div class="tt-service-process-intro">
                            <span class="tt-service-kicker">Premium Delivery Model</span>
                            <h2 class="tt-service-section-title wt-char-animation">A structured process that keeps the
                                work elegant, focused, and commercially sharp.</h2>
                            <p class="tt-service-section-copy tt-service-reveal">
                                Strong outcomes come from calm, well-managed execution. Our process is designed to reduce
                                noise, surface the right decisions early, and keep the final product aligned with business
                                value.
                            </p>
                            <div class="tt-service-process-tags tt-service-reveal">
                                <span>Discovery</span>
                                <span>Direction</span>
                                <span>Design</span>
                                <span>Build</span>
                                <span>Launch</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="tt-service-process-list">
                            <div class="tt-service-process-progress">
                                <span></span>
                            </div>
                            <article class="tt-service-process-item tt-service-reveal">
                                <span class="tt-service-process-number">01</span>
                                <div class="tt-service-process-body">
                                    <h3>Discovery and alignment</h3>
                                    <p>We understand the offer, audience, market position, and growth intent before a
                                        single layout is approved.</p>
                                </div>
                            </article>
                            <article class="tt-service-process-item tt-service-reveal">
                                <span class="tt-service-process-number">02</span>
                                <div class="tt-service-process-body">
                                    <h3>Experience and content architecture</h3>
                                    <p>We shape the right pages, flows, priorities, and messaging hierarchy so the user
                                        journey feels effortless and persuasive.</p>
                                </div>
                            </article>
                            <article class="tt-service-process-item tt-service-reveal">
                                <span class="tt-service-process-number">03</span>
                                <div class="tt-service-process-body">
                                    <h3>Visual direction and interface polish</h3>
                                    <p>Typography, spacing, motion, imagery, and interaction cues come together to create
                                        a refined, premium impression.</p>
                                </div>
                            </article>
                            <article class="tt-service-process-item tt-service-reveal">
                                <span class="tt-service-process-number">04</span>
                                <div class="tt-service-process-body">
                                    <h3>Development and integration</h3>
                                    <p>We build the product cleanly, connect the tools you rely on, and make sure the
                                        experience is reliable across devices and workflows.</p>
                                </div>
                            </article>
                            <article class="tt-service-process-item tt-service-reveal">
                                <span class="tt-service-process-number">05</span>
                                <div class="tt-service-process-body">
                                    <h3>Launch and refinement</h3>
                                    <p>Go-live is treated as a milestone, not the finish line. We review performance,
                                        tighten weak spots, and keep the experience ready for growth.</p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tt-service-pricing pb-120" id="service-pricing">
            <div class="container">
                <div class="row align-items-end mb-50">
                    <div class="col-xl-7 col-lg-8">
                        <div class="tt-service-section-heading">
                            <span class="tt-service-kicker">Pricing and Engagement</span>
                            <h2 class="tt-service-section-title wt-char-animation">Clear starting points for businesses at
                                different levels of ambition.</h2>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-4">
                        <p class="tt-service-section-copy tt-service-reveal">
                            Final pricing depends on scope, content readiness, integrations, and delivery timeline, but
                            these ranges help you plan realistically and choose the right level of investment.
                        </p>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-xl-4 col-lg-6">
                        <div class="tt-service-pricing-card tt-service-reveal tt-service-tilt-card">
                            <span class="tt-service-plan-label">Essential Presence</span>
                            <h3>From $1,500</h3>
                            <p class="tt-service-plan-meta">Ideal for founders, consultants, and growing businesses that
                                need a premium first impression fast.</p>
                            <ul>
                                <li>Discovery session and project direction</li>
                                <li>High-end landing page or focused brand-led website</li>
                                <li>Responsive build with contact funnel setup</li>
                                <li>Basic SEO structure and launch support</li>
                                <li>Two rounds of strategic refinement</li>
                            </ul>
                            <div class="tt-service-plan-footer">
                                <span>Typical timeline: 2 to 4 weeks</span>
                                <a href="{{ route('contact.show') }}">Enquire now <i
                                        class="fa-regular fa-arrow-up-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6">
                        <div class="tt-service-pricing-card featured tt-service-reveal tt-service-tilt-card">
                            <span class="tt-service-plan-badge">Most Requested</span>
                            <span class="tt-service-plan-label">Growth Build</span>
                            <h3>From $6,000</h3>
                            <p class="tt-service-plan-meta">Perfect for businesses ready for custom user journeys, mobile
                                apps, SaaS MVPs, or a more powerful web platform.</p>
                            <ul>
                                <li>Strategy workshop, UX mapping, and interface design</li>
                                <li>Custom development with dashboards or integrations</li>
                                <li>Analytics, automation, and user flow optimization</li>
                                <li>Premium motion and interaction direction</li>
                                <li>Post-launch refinement window</li>
                            </ul>
                            <div class="tt-service-plan-footer">
                                <span>Typical timeline: 5 to 8 weeks</span>
                                <a href="{{ route('contact.show') }}">Discuss your build <i
                                        class="fa-regular fa-arrow-up-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6">
                        <div class="tt-service-pricing-card tt-service-reveal tt-service-tilt-card">
                            <span class="tt-service-plan-label">Flagship Experience</span>
                            <h3>From $12,000</h3>
                            <p class="tt-service-plan-meta">For category leaders, funded startups, and companies needing
                                a premium, multi-surface digital ecosystem.</p>
                            <ul>
                                <li>End-to-end brand, product, and platform direction</li>
                                <li>Advanced UX systems, backend logic, and integrations</li>
                                <li>Multi-page or multi-product delivery roadmap</li>
                                <li>Launch asset support and stakeholder presentation-ready polish</li>
                                <li>Priority refinement and growth planning</li>
                            </ul>
                            <div class="tt-service-plan-footer">
                                <span>Typical timeline: 8 weeks and above</span>
                                <a href="{{ route('contact.show') }}">Book a strategy call <i
                                        class="fa-regular fa-arrow-up-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tt-service-addon-row tt-service-reveal">
                    <span>Popular add-ons</span>
                    <div class="tt-service-addon-list">
                        <a href="{{ route('contact.show') }}">Copywriting</a>
                        <a href="{{ route('contact.show') }}">SEO sprint</a>
                        <a href="{{ route('contact.show') }}">Admin dashboard</a>
                        <a href="{{ route('contact.show') }}">Payment integration</a>
                        <a href="{{ route('contact.show') }}">Brand refresh</a>
                        <a href="{{ route('contact.show') }}">Ongoing support</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="tt-service-faq pb-120">
            <div class="container">
                <div class="row g-5">
                    <div class="col-xl-5">
                        <div class="tt-service-section-heading">
                            <span class="tt-service-kicker">Questions Clients Ask</span>
                            <h2 class="tt-service-section-title wt-char-animation">The conversations that usually happen
                                before the work begins.</h2>
                            <p class="tt-service-section-copy tt-service-reveal">
                                Premium projects move more smoothly when the right expectations are clear from the start.
                                Here are the questions we answer most often before kickoff.
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="accordion accordion-flush tt-service-faq-accordion" id="accordionServiceFaq">
                            <div class="accordion-item tt-service-faq-item tt-service-reveal">
                                <h2 class="accordion-header">
                                    <button class="accordion-button tt-service-faq-button" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#service-faq-one" aria-expanded="true"
                                        aria-controls="service-faq-one">
                                        How do we choose the right package?
                                    </button>
                                </h2>
                                <div id="service-faq-one" class="accordion-collapse collapse show"
                                    data-bs-parent="#accordionServiceFaq">
                                    <div class="accordion-body tt-service-faq-body">
                                        We use your goals, timeline, and technical needs to guide the decision. Some
                                        projects need a polished digital presence quickly, while others need product
                                        design, integrations, and more complex delivery.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item tt-service-faq-item tt-service-reveal">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed tt-service-faq-button" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#service-faq-two"
                                        aria-expanded="false" aria-controls="service-faq-two">
                                        Can you work with our existing brand or product?
                                    </button>
                                </h2>
                                <div id="service-faq-two" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionServiceFaq">
                                    <div class="accordion-body tt-service-faq-body">
                                        Yes. We can refine what already exists, modernize the experience, preserve the
                                        strongest assets, and only rebuild what is actually limiting growth or perception.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item tt-service-faq-item tt-service-reveal">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed tt-service-faq-button" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#service-faq-three"
                                        aria-expanded="false" aria-controls="service-faq-three">
                                        Do you only design, or do you also handle development?
                                    </button>
                                </h2>
                                <div id="service-faq-three" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionServiceFaq">
                                    <div class="accordion-body tt-service-faq-body">
                                        We handle both. That means the strategy, interface, motion, engineering, testing,
                                        and launch can stay aligned under one delivery flow instead of being split across
                                        multiple vendors.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item tt-service-faq-item tt-service-reveal">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed tt-service-faq-button" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#service-faq-four"
                                        aria-expanded="false" aria-controls="service-faq-four">
                                        What happens after launch?
                                    </button>
                                </h2>
                                <div id="service-faq-four" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionServiceFaq">
                                    <div class="accordion-body tt-service-faq-body">
                                        We can support refinement, updates, analytics reviews, growth experiments, and new
                                        feature rollouts. The launch is the start of real-world learning, not the end of
                                        quality control.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tt-service-cta pb-120">
            <div class="container">
                <div class="tt-service-cta-wrap tt-service-reveal">
                    <div class="tt-service-cta-copy">
                        <span class="tt-service-kicker">Ready When You Are</span>
                        <h2 class="tt-service-section-title">If your business deserves to look more refined, credible,
                            and commercially ready, let us build the digital experience to match.</h2>
                    </div>
                    <div class="tt-service-cta-actions">
                        <a class="tt-service-btn tt-service-btn-primary wt-btn-bounce"
                            href="{{ route('contact.show') }}">Request a proposal</a>
                        <a class="tt-service-cta-phone" href="tel:+2349124948602">+2349124948602</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('/assets/js/service-luxury.js') }}"></script>
@endpush
