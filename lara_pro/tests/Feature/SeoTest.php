<?php

it('renders page specific seo metadata on public pages', function () {
    $services = [
        '/single/web' => 'Web Design Company in Abuja, Nigeria | Turance',
        '/single/mobile' => 'Mobile App Development Company | Turance',
        '/single/saas' => 'SaaS Product Development Company | Turance',
        '/single/branding' => 'Branding &amp; Identity Design Agency | Turance',
    ];

    foreach ($services as $url => $title) {
        $this->get($url)
            ->assertOk()
            ->assertSee($title, false)
            ->assertSee('<link rel="canonical" href="https://turancetechnologies.com'.$url.'">', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type": "Service"', false)
            ->assertSee('"@type": "FAQPage"', false)
            ->assertSee('"@type": "BreadcrumbList"', false)
            ->assertSee('tt-detail-hero', false)
            ->assertSee('tt-header', false)
            ->assertSee('tt-footer', false)
            ->assertDontSee('wt-breadcrumb', false);
    }
});

it('presents a search focused homepage with direct conversion paths', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Web Design &amp; Software Development Agency | Turance', false)
        ->assertSee('Excellence')
        ->assertSee('Delivered')
        ->assertSee('Get a project estimate')
        ->assertSee('data-conversion="home_hero_quote"', false)
        ->assertSee('data-mobile-sales-bar', false)
        ->assertSee('"@type": "FAQPage"', false);
});

it('prefills a valid service topic on the quote form', function () {
    $this->get('/contact?service=saas')
        ->assertOk()
        ->assertSee('<option value="SaaS Platform Development" selected>', false)
        ->assertSee('<link rel="canonical" href="https://turancetechnologies.com/contact">', false);
});

it('keeps quote links free of the contact form fragment', function () {
    foreach (['/', '/service', '/single/web', '/privacy'] as $url) {
        $this->get($url)
            ->assertOk()
            ->assertDontSee('#contact-form', false);
    }
});

it('publishes crawler discovery files', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<loc>https://turancetechnologies.com/</loc>', false)
        ->assertSee('<loc>https://turancetechnologies.com/contact</loc>', false);

    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: https://turancetechnologies.com/sitemap.xml', false)
        ->assertSee('Disallow: /admin/', false);

    $this->get('/llms.txt')
        ->assertOk()
        ->assertSee('Premium website design and development', false);
});

it('keeps legacy duplicate urls out of the index', function () {
    $this->get('/index.html')->assertRedirect('/');
    $this->get('/contact.html')->assertRedirect('/contact');
    $this->get('/index4.html')->assertRedirect('/single/branding');
    $this->get('/about.html')->assertRedirect('/#about');
    $this->get('/pricing.html')->assertRedirect('/service#service-pricing');
    $this->get('/portfolio.html')->assertRedirect('/#work');
    $this->get('/faq.html')->assertRedirect('/#faq');
});

it('does not expose missing static template pages in public navigation', function () {
    $this->get('/single/branding')
        ->assertOk()
        ->assertDontSee('.html', false);
});

it('links every detailed service from the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('services.web', absolute: false), false)
        ->assertSee(route('services.mobile', absolute: false), false)
        ->assertSee(route('services.saas', absolute: false), false)
        ->assertSee(route('services.branding', absolute: false), false)
        ->assertSee('36 Plus One')
        ->assertSee('https://www.36plusone.org/', false)
        ->assertSee('KiddoVista')
        ->assertSee('https://kiddovista.co.uk/', false)
        ->assertSee('IHcPro Store')
        ->assertSee('https://shop.ihcpro.co.uk/', false)
        ->assertSee('Abstract interface preview for 36 Plus One')
        ->assertSee('Abstract interface preview for KiddoVista')
        ->assertSee('Abstract interface preview for IHcPro Store')
        ->assertDontSee('/assets/img/project/36plusone-live.webp', false);
});

it('renders the services overview with the current public design system', function () {
    $this->get('/service')
        ->assertOk()
        ->assertSee('tt-services-overview-hero', false)
        ->assertSee('tt-capability-deck', false)
        ->assertSee('tt-header', false)
        ->assertSee('tt-footer', false)
        ->assertSee('id="service-pricing"', false)
        ->assertSee('From $500')
        ->assertSee('From $2,500')
        ->assertSee('From $6,500')
        ->assertSee('These are starting prices, not fixed packages.')
        ->assertDontSee('wt-header-area', false)
        ->assertDontSee('tt-service-hero', false);
});

it('renders linked privacy and terms pages with current public metadata', function () {
    $this->get('/privacy')
        ->assertOk()
        ->assertSee('Privacy Policy | Turance Technologies', false)
        ->assertSee('tt-legal-page', false)
        ->assertSee(route('terms.show', absolute: false), false)
        ->assertSee('tt-footer', false);

    $this->get('/terms')
        ->assertOk()
        ->assertSee('Terms of Use | Turance Technologies', false)
        ->assertSee('tt-legal-page', false)
        ->assertSee(route('privacy.show', absolute: false), false)
        ->assertSee('tt-footer', false);

    $this->get('/privacy-policy')->assertRedirect('/privacy');
    $this->get('/terms-and-conditions')->assertRedirect('/terms');
});
