@extends('layouts.master')
@section('minimal_page', 'true')
@section('seo_title', $project['name'].' — Selected work | Turance Technologies')
@section('seo_description', $project['description'])
@section('seo_canonical', route('work.show', ['slug' => $project['slug']]))
@section('seo_image', asset($project['image']))
@push('styles')
<link rel="preload" as="font" type="font/ttf" crossorigin href="{{ asset('/assets/fonts/Urbanist-Regular.ttf') }}">
<link rel="stylesheet" href="{{ asset('/assets/css/editorial-home.css') }}?v=3.5">
<link rel="stylesheet" href="{{ asset('/assets/css/editorial-project.css') }}?v=1.0">
@endpush
@push('scripts')
<script src="{{ asset('/assets/js/editorial-home.js') }}?v=1.9" defer></script>
@endpush
@section('content')
<a class="ed-skip" href="#main-content">Skip to content</a>
<div class="ed-site" id="top">
    <header class="ed-header" data-header>
        <a class="ed-brand" href="{{ route('home') }}" aria-label="Turance Technologies home"><img src="{{ asset('/assets/img/logo/logo.png') }}" width="694" height="178" alt="" decoding="async"></a>
        <nav class="ed-nav" id="ed-nav" aria-label="Primary navigation"><a href="{{ route('home') }}#work">Work</a><a href="{{ route('home') }}#services">Services</a><a href="{{ route('home') }}#about">About</a><a class="ed-nav__mobile-contact" href="{{ route('contact.show') }}">Contact <span aria-hidden="true">↗</span></a></nav>
        <a class="ed-header__contact" href="{{ route('contact.show') }}">Contact <span aria-hidden="true">✦</span></a>
        <button class="ed-menu" type="button" aria-label="Open navigation menu" aria-controls="tt-mobile-navigation" aria-expanded="false" data-menu-open><span></span><span></span></button>
    </header>
    <x-home.mobile-navigation />
    <main id="main-content">
        <article class="ed-case">
            <div class="ed-case__intro ed-section">
                <nav class="ed-case__breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ route('home') }}#work">Work</a><span>/</span><span aria-current="page">{{ $project['name'] }}</span></nav>
                <div class="ed-section__top"><span>{{ $project['number'] }} / Selected work</span><span>{{ $project['category'] }}</span></div>
                <h1 class="ed-display">{{ $project['name'] }}</h1>
                <div class="ed-case__lead"><p>{{ $project['description'] }}</p><a class="ed-circle-link" href="{{ $project['url'] }}" target="_blank" rel="noopener noreferrer"><span class="ed-circle-link__icon" aria-hidden="true">↗</span><span>View live project</span></a></div>
            </div>
            <figure class="ed-case__visual"><img src="{{ asset($project['image']) }}" alt="{{ $project['image_alt'] }}" width="1440" height="1800" fetchpriority="high" decoding="async"><figcaption>{{ $project['name'] }} / Live product</figcaption></figure>
            <div class="ed-case__details ed-section">
                <div class="ed-section__top"><span>What we built</span><span>Thoughtful digital execution</span></div>
                <div class="ed-case__details-grid"><h2 class="ed-display" data-reveal>Work with<br><em>purpose.</em></h2><div><p>{{ $project['description'] }}</p><ul aria-label="Project disciplines">@foreach ($project['disciplines'] as $discipline)<li>{{ $discipline }}</li>@endforeach</ul></div></div>
                <div class="ed-section__bottom"><a href="{{ route('home') }}#work">← Back to selected work</a><a href="{{ route('contact.show') }}">Start a project <span aria-hidden="true">↗</span></a></div>
            </div>
        </article>
        <section class="ed-section ed-contact ed-case__contact" aria-labelledby="case-contact-title">
            <div class="ed-section__top"><span>The next chapter</span><span>Have a project in mind?</span></div>
            <h2 id="case-contact-title" class="ed-display" data-reveal>Something<br><em>remarkable.</em></h2>
            <div class="ed-contact__action"><p>Tell us what you’re building.</p><a class="ed-circle-link" href="{{ route('contact.show') }}"><span>Start a conversation</span><span class="ed-circle-link__icon" aria-hidden="true">↗</span></a></div>
            <x-home.footer :embedded="true" />
        </section>
    </main>
</div>
@endsection
