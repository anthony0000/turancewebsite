@extends('layouts.master')

@section('minimal_page', 'true')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&amp;family=Hanken+Grotesk:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-reference.css') }}?v=2.4">
    <link rel="stylesheet" href="{{ asset('/assets/css/home-sections.css') }}?v=2.1">
    <link rel="stylesheet" href="{{ asset('/assets/css/legal-reference.css') }}?v=1.0">
@endpush

@section('content')
    <a class="tt-skip-link" href="#main-content">Skip to main content</a>

    <main class="tt-reference-home tt-legal-page" id="main-content">
        <x-home.header />

        <section class="tt-legal-hero" aria-labelledby="legal-title">
            <div class="tt-legal-hero__geometry" aria-hidden="true"><i></i><i></i><i></i><span>TT / {{ $legal['type'] }}</span></div>
            <div class="tt-section__inner tt-legal-hero__inner">
                <nav class="tt-legal-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span>/</span><span>Legal</span><span>/</span><span aria-current="page">{{ $legal['type'] }}</span>
                </nav>
                <p class="tt-legal-kicker">{{ $legal['eyebrow'] }}</p>
                <h1 id="legal-title">{{ $legal['title'] }}</h1>
                <div class="tt-legal-hero__meta">
                    <p>{{ $legal['intro'] }}</p>
                    <span>Last updated {{ $legal['updated'] }}</span>
                </div>
            </div>
        </section>

        <section class="tt-legal-content tt-section" aria-label="{{ $legal['type'] }} details">
            <div class="tt-section__inner tt-legal-content__inner">
                <aside class="tt-legal-index">
                    <span>On this page</span>
                    <ol>
                        @foreach ($legal['sections'] as $section)
                            <li><a href="#legal-section-{{ $loop->iteration }}">{{ $section['title'] }}</a></li>
                        @endforeach
                    </ol>
                </aside>
                <article class="tt-legal-article">
                    @foreach ($legal['sections'] as $section)
                        <section id="legal-section-{{ $loop->iteration }}" class="tt-legal-section" aria-labelledby="legal-section-title-{{ $loop->iteration }}">
                            <span class="tt-legal-section__number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h2 id="legal-section-title-{{ $loop->iteration }}">{{ $section['title'] }}</h2>
                                <p>{{ $section['body'] }}</p>
                            </div>
                        </section>
                    @endforeach
                    <p class="tt-legal-note">This page is general website information, not a substitute for advice about your particular circumstances.</p>
                </article>
            </div>
        </section>

        <section class="tt-legal-cta" aria-labelledby="legal-cta-title">
            <div class="tt-section__inner tt-legal-cta__inner">
                <div>
                    <span class="tt-section-heading__eyebrow"><i aria-hidden="true"></i>Questions?</span>
                    <h2 id="legal-cta-title">Need to talk something through?</h2>
                </div>
                <a class="tt-primary-button" href="{{ route('contact.show') }}">Contact our team <i aria-hidden="true"><svg viewBox="0 0 20 20"><path d="M4 10h12M11 5l5 5-5 5" /></svg></i></a>
            </div>
        </section>

        <x-home.footer />
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('/assets/js/home-reference.js') }}?v=2.5" defer></script>
@endpush
