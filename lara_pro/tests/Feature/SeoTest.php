<?php

it('renders page specific seo metadata on public pages', function () {
    $services = [
        '/single/web' => 'Website Design and Development Services | Turance Technologies',
        '/single/mobile' => 'Mobile App Development Services | Turance Technologies',
        '/single/saas' => 'SaaS Product Design and Development | Turance Technologies',
        '/single/branding' => 'Branding and Identity Design Services | Turance Technologies',
    ];

    foreach ($services as $url => $title) {
        $this->get($url)
            ->assertOk()
            ->assertSee($title, false)
            ->assertSee('<link rel="canonical" href="https://turancetechnologies.com'.$url.'">', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type": "Service"', false)
            ->assertSee('tt-detail-hero', false)
            ->assertSee('tt-header', false)
            ->assertSee('tt-footer', false)
            ->assertDontSee('wt-breadcrumb', false);
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
        ->assertSee('/assets/img/project/36plusone-live.webp', false);
});

it('renders the services overview with the current public design system', function () {
    $this->get('/service')
        ->assertOk()
        ->assertSee('tt-services-overview-hero', false)
        ->assertSee('tt-capability-deck', false)
        ->assertSee('tt-header', false)
        ->assertSee('tt-footer', false)
        ->assertSee('id="service-pricing"', false)
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
