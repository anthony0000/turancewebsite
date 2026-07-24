<!DOCTYPE html>
<html lang="en">

<head>
    @php
        $siteUrl = rtrim(config('seo.site_url'), '/');
        $routeName = optional(request()->route())->getName();
        $fallbackRoutes = [
            'contact.html' => 'contact.show',
        ];
        $seoRouteName = $routeName ?: ($fallbackRoutes[trim(request()->path(), '/')] ?? 'home');
        $seoPages = config('seo.pages', []);
        $pageSeo = $seoPages[$seoRouteName] ?? config('seo.default');
        $title = html_entity_decode(
            trim($__env->yieldContent('seo_title', $pageSeo['title'] ?? config('seo.default.title'))),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $description = trim($__env->yieldContent('seo_description', $pageSeo['description'] ?? config('seo.default.description')));
        $image = trim($__env->yieldContent('seo_image', config('seo.image')));
        $robots = trim($__env->yieldContent('seo_robots', 'index, follow, max-image-preview:large'));
        $canonicalOverride = trim($__env->yieldContent('seo_canonical', ''));
        $canonicalPath = route($pageSeo['route'] ?? 'home', [], false);
        $canonical = $canonicalOverride ?: $siteUrl.'/'.ltrim($canonicalPath, '/');
        $keywords = implode(', ', config('seo.keywords', []));
        $serviceItems = collect(config('seo.service_items', []))->values();

        $organization = [
            '@type' => 'ProfessionalService',
            '@id' => $siteUrl.'/#organization',
            'name' => config('seo.site_name'),
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => config('seo.logo'),
                'contentUrl' => config('seo.logo'),
                'width' => 694,
                'height' => 178,
            ],
            'image' => config('seo.image'),
            'slogan' => config('seo.tagline'),
            'description' => config('seo.default.description'),
            'email' => config('seo.email'),
            'telephone' => config('seo.phone'),
            'priceRange' => '$$',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('seo.address.street'),
                'addressLocality' => config('seo.address.city'),
                'addressRegion' => config('seo.address.region'),
                'addressCountry' => config('seo.address.country'),
            ],
            'areaServed' => array_map(
                fn ($country) => ['@type' => 'Country', 'name' => $country],
                config('seo.area_served', [])
            ),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'sales',
                'email' => config('seo.email'),
                'telephone' => config('seo.phone'),
                'areaServed' => config('seo.area_served', []),
                'availableLanguage' => ['English'],
            ],
            'knowsAbout' => $serviceItems->pluck('name')->all(),
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Digital product and brand services',
                'itemListElement' => $serviceItems
                    ->map(fn ($service) => [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => $service['name'],
                            'description' => $service['description'],
                            'url' => $siteUrl.'/'.ltrim(route($service['route'], [], false), '/'),
                        ],
                    ])
                    ->all(),
            ],
        ];

        $schemaGraph = [
            [
                '@type' => 'WebSite',
                '@id' => $siteUrl.'/#website',
                'url' => $siteUrl,
                'name' => config('seo.site_name'),
                'description' => config('seo.default.description'),
                'publisher' => ['@id' => $siteUrl.'/#organization'],
                'inLanguage' => 'en',
            ],
            $organization,
            [
                '@type' => 'WebPage',
                '@id' => $canonical.'#webpage',
                'url' => $canonical,
                'name' => $title,
                'description' => $description,
                'isPartOf' => ['@id' => $siteUrl.'/#website'],
                'about' => ['@id' => $siteUrl.'/#organization'],
                'primaryImageOfPage' => [
                    '@type' => 'ImageObject',
                    'url' => $image,
                ],
                'inLanguage' => 'en',
            ],
        ];

        if (! empty($pageSeo['service_type'])) {
            $schemaGraph[] = [
                '@type' => 'Service',
                '@id' => $canonical.'#service',
                'name' => $pageSeo['service_type'],
                'serviceType' => $pageSeo['service_type'],
                'description' => $description,
                'url' => $canonical,
                'provider' => ['@id' => $siteUrl.'/#organization'],
                'areaServed' => $organization['areaServed'],
            ];
        }

        if (in_array($seoRouteName, ['home', 'service.show'], true)) {
            $schemaGraph[] = [
                '@type' => 'ItemList',
                '@id' => $siteUrl.'/service#services',
                'name' => 'Turance Technologies services',
                'itemListElement' => $serviceItems
                    ->map(fn ($service, $index) => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $service['name'],
                        'url' => $siteUrl.'/'.ltrim(route($service['route'], [], false), '/'),
                        'description' => $service['description'],
                    ])
                    ->all(),
            ];
        }

        $breadcrumbItems = collect([
            ['name' => 'Home', 'url' => $siteUrl.'/'],
        ]);

        if (! empty($pageSeo['parent_route'])) {
            $parentSeo = $seoPages[$pageSeo['parent_route']] ?? null;

            if ($parentSeo) {
                $breadcrumbItems->push([
                    'name' => $parentSeo['breadcrumb'] ?? $parentSeo['title'],
                    'url' => $siteUrl.'/'.ltrim(route($parentSeo['route'], [], false), '/'),
                ]);
            }
        }

        if ($seoRouteName !== 'home') {
            $breadcrumbItems->push([
                'name' => $pageSeo['breadcrumb'] ?? $title,
                'url' => $canonical,
            ]);
        }

        if ($breadcrumbItems->count() > 1) {
            $schemaGraph[] = [
                '@type' => 'BreadcrumbList',
                '@id' => $canonical.'#breadcrumb',
                'itemListElement' => $breadcrumbItems
                    ->values()
                    ->map(fn ($item, $index) => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $item['name'],
                        'item' => $item['url'],
                    ])
                    ->all(),
            ];
        }

        $pageFaqs = match (true) {
            $seoRouteName === 'home' => config('seo.home_faqs', []),
            $seoRouteName === 'service.show' => config('seo.services_faqs', []),
            str_starts_with((string) $seoRouteName, 'services.') => config(
                'service-pages.'.\Illuminate\Support\Str::after((string) $seoRouteName, 'services.').'.faqs',
                []
            ),
            default => [],
        };

        if (! empty($pageFaqs)) {
            $schemaGraph[] = [
                '@type' => 'FAQPage',
                '@id' => $canonical.'#faq',
                'mainEntity' => collect($pageFaqs)
                    ->map(fn ($faq) => [
                        '@type' => 'Question',
                        'name' => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['answer'],
                        ],
                    ])
                    ->all(),
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => $schemaGraph,
        ];
    @endphp
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="author" content="{{ config('seo.site_name') }}">
    <meta name="theme-color" content="#071426">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="en" href="{{ $canonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonical }}">

    <meta property="og:site_name" content="{{ config('seo.site_name') }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:alt" content="{{ $title }}">
    <meta property="og:locale" content="{{ config('seo.locale') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @stack('structured_data')

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/assets/img/logo/favicon.png') }}">

    @php($minimalPage = trim($__env->yieldContent('minimal_page', 'false')) === 'true')

    @unless ($minimalPage)
        <!-- Shared site CSS -->
        <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/animate.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/swiper-bundle.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/slick.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/font-awesome-pro.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/flaticon_omio.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/spacing.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/css/main.css') }}?v=1.3">
    @endunless
    @stack('styles')
</head>

<body class="{{ $minimalPage ? 'tt-minimal-page' : 'page-wrapper wt-magic-cursor' }}">
    <!-- Start Content Area-->

    @unless ($minimalPage)
    <!-- header area start -->
    <header class="wt-header-height">
        <div id="header-sticky" class="wt-header-area wt-header-transparent">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="wt-header-wrapper">

                            <!-- logo -->
                            <div class="wt-header-logo">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('/assets/img/logo/logo.png') }}"
                                        alt="Turance Technologies">
                                </a>
                            </div>

                            <!-- toggle -->
                            <div class="wt-header-menu">
                                <button class="wt-offcanvas-open-btn">
                                    <span>
                                        <svg width="15" height="15" viewbox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.5 5C3.88071 5 5 3.88071 5 2.5C5 1.11929 3.88071 0 2.5 0C1.11929 0 0 1.11929 0 2.5C0 3.88071 1.11929 5 2.5 5ZM2.5 15C3.88071 15 5 13.8807 5 12.5C5 11.1193 3.88071 10 2.5 10C1.11929 10 0 11.1193 0 12.5C0 13.8807 1.11929 15 2.5 15ZM15 2.5C15 3.88071 13.8807 5 12.5 5C11.1193 5 10 3.88071 10 2.5C10 1.11929 11.1193 0 12.5 0C13.8807 0 15 1.11929 15 2.5ZM12.5 15C13.8807 15 15 13.8807 15 12.5C15 11.1193 13.8807 10 12.5 10C11.1193 10 10 11.1193 10 12.5C10 13.8807 11.1193 15 12.5 15Z"
                                                fill="white"></path>
                                        </svg>
                                    </span>
                                    menu
                                </button>
                            </div>

                            <!-- menu -->
                            <div class="d-none">
                                <nav class="wt-main-menu-content">
                                    <ul>
                                        <li class="has-dropdown"><a href="{{ route('home') }}">Home</a>
                                            <div class="wt-submenu submenu dark has-homemenu">
                                                <div
                                                    class="row gx-6 row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-3">
                                                    <div class="col homemenu mb-30">
                                                        <div class="homemenu-thumb mb-15">
                                                            <img src="{{ asset('/assets/img/menu/home1.jpg') }}"
                                                                alt="Turance Technologies homepage preview">
                                                            <div class="homemenu-btn">
                                                                <a class="menu-btn show-1"
                                                                    href="{{ route('home') }}">View Page</a>
                                                            </div>
                                                        </div>
                                                        <div class="homemenu-content text-center">
                                                            <h4 class="homemenu-title">
                                                                <a href="{{ route('home') }}">Home 01</a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="col homemenu mb-30">
                                                        <div class="homemenu-thumb mb-15">
                                                            <img src="{{ asset('/assets/img/menu/home2.jpg') }}"
                                                                alt="Website design preview">
                                                            <div class="homemenu-btn">
                                                                <a class="menu-btn show-1"
                                                                    href="{{ route('services.web') }}">View
                                                                    Page</a>
                                                            </div>
                                                        </div>
                                                        <div class="homemenu-content text-center">
                                                            <h4 class="homemenu-title">
                                                                <a href="{{ route('services.web') }}">Website Design</a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="col homemenu mb-30">
                                                        <div class="homemenu-thumb mb-15">
                                                            <img src="{{ asset('/assets/img/menu/home3.jpg') }}"
                                                                alt="Mobile and SaaS product preview">
                                                            <div class="homemenu-btn">
                                                                <a class="menu-btn show-1"
                                                                    href="{{ route('services.mobile') }}">View
                                                                    Page</a>
                                                            </div>
                                                        </div>
                                                        <div class="homemenu-content text-center">
                                                            <h4 class="homemenu-title">
                                                                <a href="{{ route('services.mobile') }}">Mobile Products</a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="col homemenu mb-30">
                                                        <div class="homemenu-thumb mb-15">
                                                            <img src="{{ asset('/assets/img/menu/home4.jpg') }}"
                                                                alt="Brand identity preview">
                                                            <div class="homemenu-btn">
                                                                <a class="menu-btn show-1"
                                                                    href="{{ route('services.branding') }}">View
                                                                    Page</a>
                                                            </div>
                                                        </div>
                                                        <div class="homemenu-content text-center">
                                                            <h4 class="homemenu-title">
                                                                <a href="{{ route('services.branding') }}">Brand Identity</a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="has-dropdown"><a href="{{ route('service.show') }}">services</a>
                                            <ul class="submenu dark wt-submenu">
                                                <li><a href="{{ route('service.show') }}">all services</a></li>
                                                <li><a href="{{ route('services.web') }}">website design</a></li>
                                                <li><a href="{{ route('services.mobile') }}">mobile apps</a></li>
                                                <li><a href="{{ route('services.saas') }}">saas products</a></li>
                                                <li><a href="{{ route('services.branding') }}">branding</a></li>
                                            </ul>
                                        </li>
                                        <li class="has-dropdown"><a href="{{ route('home') }}#about">Company</a>
                                            <ul class="submenu dark wt-submenu">
                                                <li><a href="{{ route('home') }}#about">about</a></li>
                                                <li><a href="{{ route('home') }}#work">selected work</a></li>
                                                <li><a href="{{ route('home') }}#perspectives">client perspectives</a></li>
                                                <li><a href="{{ route('service.show') }}#service-pricing">pricing</a></li>
                                                <li><a href="{{ route('home') }}#faq">faq</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="{{ route('contact.show') }}">Contact</a></li>
                                    </ul>
                                </nav>
                            </div>

                            <!-- mail -->
                            <div class="wt-header-mail d-none d-lg-block">
                                <a href="mailto:{{ config('seo.email') }}">{{ config('seo.email') }}</a>
                            </div>

                            <!-- social -->
                            <div class="wt-header-social d-none d-sm-block">
                                <ul>
                                    <li>
                                        <input class="wt-header-check-btn" type="checkbox">
                                        <a href="{{ url('#') }}"><i
                                                class="fa-sharp fa-regular fa-share-nodes"></i> Social</a>
                                        <ul class="submenu">
                                            <li><a href="{{ url('#') }}">Instagram</a></li>
                                            <li><a href="{{ url('#') }}">LinkedIn</a></li>
                                            <li><a href="{{ url('#') }}">Facebook</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <!-- button -->
                            <div class="wt-header-buton icon_main d-none d-sm-block">
                                <a class="wt-header-btn" href="{{ route('contact.show') }}">Inquiries
                                    <span class="icon_box">
                                        <i class="icon_first fa-regular fa-arrow-right"></i>
                                        <i class="icon_second fa-regular fa-arrow-right"></i>
                                    </span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header area end -->
    @endunless

    @if ($minimalPage)
        @yield('content')
        @if (request()->routeIs('home', 'service.show', 'services.*'))
            <aside class="tt-mobile-sales-bar" aria-label="Project enquiry shortcuts" data-mobile-sales-bar>
                <a href="{{ config('seo.whatsapp_url') }}" target="_blank" rel="noopener noreferrer"
                    data-conversion="mobile_sales_whatsapp">WhatsApp</a>
                <a href="{{ route('contact.show') }}"
                    data-conversion="mobile_sales_quote">Get an estimate
                    <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4 10h12M11 5l5 5-5 5" /></svg>
                </a>
            </aside>
        @endif
    @else
    <div id="smooth-wrapper">

        <div id="smooth-content">
            @yield('content')

            <!-- Footer area start -->
            <footer class="wt-footer-area fix">
                <div class="wt-footer-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-2 col-lg-2">
                                <div class="wt-footer-info mt-55 wt_fade_anim" data-delay=".3">
                                    <div class="wt-footer-logo mb-20">
                                        <a href="{{ route('home') }}">
                                            <img src="{{ asset('/assets/img/logo/logo.png') }}" height="45"
                                                alt="Turance Technologies">
                                        </a>
                                    </div>
                                    <div class="wt-footer-content">
                                        <p class="text-center">Premium web, app, SaaS, and branding agency.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9">
                                <div class="wt-footer-widget-top text-center wt_fade_anim" data-delay=".5">
                                    <div class="wt-section-wrapper">
                                        <h2 class="wt-section-title wt-text-invert">want to <br> work together
                                        </h2>
                                    </div>
                                    <div class="wt-footer-widget-border"></div>
                                    <div class="wt-footer-widget-mail">
                                        <h6>email us</h6>
                                        <a href="mailto:{{ config('seo.email') }}">{{ config('seo.email') }}</a>
                                    </div>
                                    <div class="wt-footer-right-social">
                                        <ul>
                                            <li>
                                                <a href="{{ url('#') }}">
                                                    <span class="active-media">Dribbble</span>
                                                    <span class="hover-media"><i
                                                            class="fa-brands fa-dribbble"></i></span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('#') }}">
                                                    <span class="active-media">LnkedIn</span>
                                                    <span class="hover-media"><i
                                                            class="fa-brands fa-linkedin-in"></i></span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('#') }}">
                                                    <span class="active-media">Behance</span>
                                                    <span class="hover-media"><i
                                                            class="fa-brands fa-behance"></i></span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('#') }}">
                                                    <span class="active-media">Twitter</span>
                                                    <span class="hover-media"><i
                                                            class="fa-brands fa-twitter"></i></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-1 col-lg-1">
                                <div class="wt-footer-widget-menu">
                                    <div class="wt-header-menu mt-40 wt_fade_anim" data-delay=".7">
                                        <ul>
                                            <li>
                                                <a href="{{ url('#') }}"><img
                                                        src="{{ asset('/assets/img/icon/menu.svg') }}"
                                                        alt="Open Turance Technologies menu">
                                                    menu</a>
                                                <ul class="submenu">
                                                    <li><a href="{{ route('home') }}">Home</a></li>
                                                    <li><a href="{{ route('service.show') }}">Services</a></li>
                                                    <li><a href="{{ route('home') }}#about">About</a></li>
                                                    <li><a href="{{ route('contact.show') }}">Contact</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wt-footer-center">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <div class="wt-about-bottom-content text-center">
                                    <h2 class="wt-about-title">Turance Technologies</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wt-footer-bottom">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col">
                                <div class="wt-footer-copyright text-center">
                                    <p class="wt-footer-copyright-pagaraph">Copyright © {{ date('Y') }} <a
                                            href="{{ url('#') }}">Turance tech</a>. All
                                        Rights Reserved.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- Footer area end -->
        </div>
    </div>
    <!-- End Contenet Area -->
    @endif

    @unless ($minimalPage)
    <!-- Start Script Area -->
    <!-- pre loader area start -->
    <div class="preloader">
        <svg viewbox="0 0 1000 1000" preserveaspectratio="none">
            <path id="preloaderSvg" d="M0,1005S175,995,500,995s500,5,500,5V0H0Z"></path>
        </svg>
        <div class="preloader-heading">
            <div class="load-text">
                <span>L</span>
                <span>o</span>
                <span>a</span>
                <span>d</span>
                <span>i</span>
                <span>n</span>
                <span>g</span>
            </div>
        </div>
    </div>
    <!-- pre loader area end --><!-- cursor to top start -->
    <div class="cursor"></div>
    <div class="cursor2"></div>
    <!-- cursor to top end -->


    <!-- Magic cursor start -->
    <div id="magic-cursor">
        <div id="ball"></div>
    </div>
    <!-- Magic cursor end -->
    <!-- offcanvas area end -->
    <div class="wt-offcanvas-2-area p-relative">
        <div class="wt-offcanvas-2-bg is-left left-box"></div>
        <div class="wt-offcanvas-2-bg is-right right-box d-none d-md-block"></div>
        <div class="wt-offcanvas-2-wrapper">
            <div class="wt-offcanvas-2-left left-box">
                <div class="wt-offcanvas-2-left-wrap d-flex justify-content-between align-items-center">
                    <div class="wtoffcanvas__logo">
                        <a class="logo-1" href="{{ route('home') }}"><img
                                src="{{ asset('/assets/img/logo/logo.png') }}" alt="Turance Technologies"></a>
                    </div>
                    <div class="wt-offcanvas-2-close d-md-none text-end">
                        <button class="wt-offcanvas-2-close-btn wt-offcanvas-2-close-btn">
                            <span class="text">
                                <span>close</span>
                            </span>
                            <span class="d-inline-block">
                                <span>
                                    <svg width="24" height="24" viewbox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect width="32.621" height="1.00918"
                                            transform="matrix(0.704882 0.709325 -0.704882 0.709325 1.0061 0)"
                                            fill="currentcolor"></rect>
                                        <rect width="32.621" height="1.00918"
                                            transform="matrix(0.704882 -0.709325 0.704882 0.709325 0 23.2842)"
                                            fill="currentcolor"></rect>
                                    </svg>
                                </span>
                            </span>

                        </button>
                    </div>
                </div>
                <div class="wt-main-menu-mobile menu-hover-active counter-row">
                    <nav></nav>
                </div>
            </div>
            <div class="wt-offcanvas-2-right right-box d-none d-md-block p-relative">
                <div class="wt-offcanvas-2-close text-end">
                    <button class="wt-offcanvas-2-close-btn">
                        <span class="text">
                            <span>close</span>
                        </span>
                        <span class="d-inline-block">
                            <span>
                                <svg width="38" height="38" viewbox="0 0 38 38" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.80859 9.80762L28.1934 28.1924" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M9.80859 28.1924L28.1934 9.80761" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                        </span>

                    </button>
                </div>
                <div class="wt-offcanvas-2-right-inner d-flex flex-column justify-content-between h-100">
                    <div class="wtoffcanvas__contact-info">
                        <div class="wtoffcanvas__contact-title">
                            <h5>Contact us</h5>
                        </div>
                        <ul>
                            <li>
                                <i class="fa-solid fa-location-dot"></i>
                                <a href="https://www.google.com/maps/@23.8223586,90.3661283,15z" target="_blank">No 3
                                    Ademola Adetokunbo crescent, Abuja</a>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:{{ config('seo.email') }}">
                                    <span class="__cf_email__">{{ config('seo.email') }}</span>
                                </a>
                            </li>
                            <li>
                                <i class="fa-brands fa-whatsapp"></i>
                                <a href="{{ config('seo.whatsapp_url') }}" target="_blank"
                                    rel="noopener noreferrer">{{ config('seo.phone') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="wt-footer-2-social wt-footer-right-social d-none d-sm-block">
                        <ul>
                            <li>
                                <a href="{{ url('#') }}">
                                    <span class="active-media">Facebook</span>
                                    <span class="hover-media"><i class="fa-brands fa-facebook-f"></i></span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('#') }}">
                                    <span class="active-media">LinkedIn</span>
                                    <span class="hover-media"><i class="fa-brands fa-linkedin-in"></i></span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('#') }}">
                                    <span class="active-media">Instagram</span>
                                    <span class="hover-media"><i class="fa-brands fa-instagram"></i></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- offcanvas area start --><!-- back to top start -->
    <div class="back-to-top-wrapper">
        <button id="back_to_top" type="button" class="back-to-top-btn">
            <svg width="12" height="7" viewbox="0 0 12 7" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M11 6L6 1L1 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round"></path>
            </svg>
        </button>
    </div>
    <!-- back to top end -->


    <script src="{{ asset('/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/js/vendor/waypoints.js') }}"></script>
    <script src="{{ asset('/assets/js/bootstrap-bundle.js') }}"></script>
    <script src="{{ asset('/assets/js/gsap/gsap.js') }}"></script>
    <script src="{{ asset('/assets/js/gsap/gsap-scroll-to-plugin.js') }}"></script>
    <script src="{{ asset('/assets/js/gsap/gsap-scroll-smoother.js') }}"></script>
    <script src="{{ asset('/assets/js/gsap/gsap-scroll-trigger.js') }}"></script>
    <script src="{{ asset('/assets/js/gsap/gsap-split-text.js') }}"></script>
    <script src="{{ asset('/assets/js/chroma.min.js') }}"></script>
    <script src="{{ asset('/assets/js/scroll-magic.js') }}"></script>
    <script src="{{ asset('/assets/js/countdown.js') }}"></script>
    <script src="{{ asset('/assets/js/swiper-bundle.js') }}"></script>
    <script src="{{ asset('/assets/js/text-slide.js') }}"></script>
    <script src="{{ asset('/assets/js/slick.js') }}"></script>
    <script src="{{ asset('/assets/js/range-slider.js') }}"></script>
    <script src="{{ asset('/assets/js/magnific-popup.js') }}"></script>
    <script src="{{ asset('/assets/js/nice-select.js') }}"></script>
    <script src="{{ asset('/assets/js/purecounter.js') }}"></script>
    <script src="{{ asset('/assets/js/wow.js') }}"></script>
    <script src="{{ asset('/assets/js/vanilla-tilt.min.js') }}"></script>
    <script src="{{ asset('/assets/js/isotope-pkgd.js') }}"></script>
    <script src="{{ asset('/assets/js/imagesloaded-pkgd.js') }}"></script>
    <script src="{{ asset('/assets/js/ajax-form.js') }}"></script>
    <script src="{{ asset('/assets/js/slider-active.js') }}"></script>
    <script src="{{ asset('/assets/js/main.js') }}?v=1.4"></script>
    <script src="{{ asset('/assets/js/wt-cursor.js') }}"></script>
    @endunless
    @stack('scripts')
</body>

</html>
