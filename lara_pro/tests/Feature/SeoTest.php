<?php

it('renders page specific seo metadata on public pages', function () {
    $this->get('/single/web')
        ->assertOk()
        ->assertSee('Website Design and Development Services | Turance Technologies', false)
        ->assertSee('<meta name="description" content="Premium website design and development', false)
        ->assertSee('<link rel="canonical" href="https://turancetechnologies.com/single/web">', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type": "Service"', false);
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
    $this->get('/about.html')->assertNotFound();
});
