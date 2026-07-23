@extends('layouts.master')

@section('minimal_page', 'true')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&amp;family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-reference.css') }}?v=1.8">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-sections.css') }}?v=1.8">
    <link rel="stylesheet" href="{{ asset('/assets/css/contact-reference.css') }}?v=1.1">
@endpush

@section('content')
    @php($topics = config('contact.topics', []))

    <a class="tt-skip-link" href="#main-content">Skip to main content</a>

    <main class="tt-reference-home tt-contact-page" id="main-content">
        <x-home.header />

        <section class="tt-contact-hero" id="contact" aria-labelledby="contact-page-title">
            <div class="tt-contact-hero__geometry" aria-hidden="true">
                <span class="tt-contact-hero__orbit tt-contact-hero__orbit--one"></span>
                <span class="tt-contact-hero__orbit tt-contact-hero__orbit--two"></span>
                <i class="tt-contact-hero__node tt-contact-hero__node--one"></i>
                <i class="tt-contact-hero__node tt-contact-hero__node--two"></i>
                <i class="tt-contact-hero__node tt-contact-hero__node--three"></i>
                <span class="tt-contact-hero__object"><img src="{{ asset('/assets/img/hero/hero-bg-shape3.png') }}" width="250" height="250" alt="" loading="lazy" decoding="async"></span>
            </div>

            <div class="tt-section__inner tt-contact-hero__inner">
                <div class="tt-contact-hero__intro">
                    <span class="tt-section-heading__eyebrow" data-reveal><i aria-hidden="true"></i>Start a conversation</span>
                    <h1 id="contact-page-title" data-reveal>Let’s shape what comes next.</h1>
                    <p data-reveal>Tell us what you are building, improving or trying to solve. We will help you identify a clear and practical next step.</p>

                    <dl class="tt-contact-hero__details" data-reveal>
                        <div>
                            <dt>Email</dt>
                            <dd><a href="mailto:{{ config('seo.email') }}">{{ config('seo.email') }}</a></dd>
                        </div>
                        <div>
                            <dt>Telephone</dt>
                            <dd><a href="tel:{{ preg_replace('/\s+/', '', config('seo.phone')) }}">{{ config('seo.phone') }}</a></dd>
                        </div>
                        <div>
                            <dt>Studio</dt>
                            <dd>{{ config('seo.address.city') }}, {{ config('seo.address.region') }} &middot; {{ config('seo.address.country') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="tt-contact-form-shell" data-reveal>
                    <div class="tt-contact-form-shell__top">
                        <div>
                            <span>Project enquiry</span>
                            <h2>Tell us about the ambition.</h2>
                        </div>
                        <span class="tt-contact-form-shell__index" aria-hidden="true">01</span>
                    </div>

                    @if ($errors->any())
                        <div class="tt-contact-alert tt-contact-alert--error" role="alert" tabindex="-1" data-form-alert>
                            <strong>Please review the highlighted details.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('contact_success'))
                        <div class="tt-contact-alert tt-contact-alert--success" role="status" tabindex="-1" data-form-alert>
                            <strong>Message received.</strong>
                            <p>{{ session('contact_success') }}</p>
                        </div>
                    @endif

                    <form id="contact-form" action="{{ route('contact.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="contact_context" value="{{ $contactContext }}">
                        <div class="tt-contact-trap" aria-hidden="true">
                            <label for="company-fax">Leave this field empty</label>
                            <input type="text" id="company-fax" name="company_fax" value=""
                                tabindex="-1" autocomplete="off">
                        </div>

                        <div class="tt-contact-form__row">
                            <div class="tt-contact-field {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label for="name">Full name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Your full name" required autocomplete="name"
                                    aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}">
                                @error('name')<small id="name-error">{{ $message }}</small>@enderror
                            </div>

                            <div class="tt-contact-field {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label for="email">Email address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    placeholder="you@company.com" required autocomplete="email"
                                    aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}">
                                @error('email')<small id="email-error">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="tt-contact-field {{ $errors->has('topic') ? 'has-error' : '' }}">
                            <label for="topic">What would you like to discuss?</label>
                            <span class="tt-contact-select">
                                <select id="topic" name="topic" required
                                    aria-describedby="{{ $errors->has('topic') ? 'topic-error' : '' }}">
                                    <option value="" disabled {{ old('topic') ? '' : 'selected' }}>Select a project type</option>
                                    @foreach ($topics as $topic)
                                        <option value="{{ $topic }}" {{ old('topic') === $topic ? 'selected' : '' }}>{{ $topic }}</option>
                                    @endforeach
                                </select>
                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="m6 8 4 4 4-4" /></svg>
                            </span>
                            @error('topic')<small id="topic-error">{{ $message }}</small>@enderror
                        </div>

                        <div class="tt-contact-field {{ $errors->has('message') ? 'has-error' : '' }}">
                            <label for="message">Project context</label>
                            <textarea name="message" rows="5" id="message"
                                placeholder="What are you building, improving or trying to solve?" required
                                aria-describedby="message-guidance {{ $errors->has('message') ? 'message-error' : '' }}">{{ old('message') }}</textarea>
                            <span class="tt-contact-field__guidance" id="message-guidance">A few clear details are enough to begin.</span>
                            @error('message')<small id="message-error">{{ $message }}</small>@enderror
                        </div>

                        @if (config('contact.turnstile.enabled') && filled(config('contact.turnstile.site_key')))
                            <div class="tt-contact-turnstile">
                                <div class="cf-turnstile" data-sitekey="{{ config('contact.turnstile.site_key') }}"
                                    data-theme="light" data-response-field-name="cf-turnstile-response"></div>
                                @error('cf-turnstile-response')<small>{{ $message }}</small>@enderror
                            </div>
                        @endif

                        <div class="tt-contact-form__submit">
                            <button class="tt-contact-submit" type="submit">
                                <span>Send enquiry</span>
                                <i aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5" /></svg></i>
                            </button>
                            <p>By sending this form, you agree that we may use your details to respond to this enquiry.</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="tt-contact-next" aria-labelledby="contact-next-title">
            <div class="tt-section__inner">
                <header class="tt-contact-next__heading" data-reveal>
                    <span class="tt-section-heading__eyebrow"><i aria-hidden="true"></i>What happens next</span>
                    <h2 id="contact-next-title">A straightforward first step.</h2>
                    <p>No lengthy sales process. We begin by understanding the context and deciding whether there is a strong fit.</p>
                </header>

                <ol class="tt-contact-next__steps">
                    <li data-reveal><span>01</span><h3>We review the brief</h3><p>Your goals, current position and the problem worth solving.</p></li>
                    <li data-reveal><span>02</span><h3>We arrange a conversation</h3><p>A focused discussion around scope, priorities and useful next steps.</p></li>
                    <li data-reveal><span>03</span><h3>We shape the engagement</h3><p>A clear recommendation covering approach, timing and commercial scope.</p></li>
                </ol>

                <div class="tt-contact-location" data-reveal>
                    <div class="tt-contact-location__copy">
                        <span>Abuja studio</span>
                        <h2>Based in Nigeria.<br>Working without borders.</h2>
                        <address>
                            {{ config('seo.address.street') }}<br>
                            {{ config('seo.address.city') }}, {{ config('seo.address.region') }}
                        </address>
                        <a class="tt-text-link" href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode(config('seo.address.street').', '.config('seo.address.city')) }}"
                            target="_blank" rel="noopener noreferrer">Open in Google Maps
                            <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                        </a>
                    </div>
                    <div class="tt-contact-location__map">
                        <iframe title="Turance Technologies location in Abuja"
                            src="https://www.google.com/maps?q=No%203%20Ademola%20Adetokunbo%20crescent%2C%20Abuja&amp;output=embed"
                            width="800" height="560" loading="lazy" allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <span aria-hidden="true"><i></i></span>
                    </div>
                </div>
            </div>
        </section>

        <x-home.footer />
    </main>
@endsection

@push('scripts')
    @if (config('contact.turnstile.enabled') && filled(config('contact.turnstile.site_key')))
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <script src="{{ asset('/assets/js/home-reference.js') }}?v=2.3" defer></script>
    <script src="{{ asset('/assets/js/contact-reference.js') }}?v=1.0" defer></script>
@endpush
