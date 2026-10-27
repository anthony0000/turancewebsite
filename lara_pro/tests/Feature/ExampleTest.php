<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response
        ->assertStatus(200)
        ->assertSee('Excellence<br>Delivered', false)
        ->assertSee('Start your project')
        ->assertSee('turance-editorial-hero-clean.png', false)
        ->assertDontSee('Web Authority Sharper')
        ->assertDontSee('home-luxury');
});

it('shows attributed client testimonials on the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('id="testimonials"', false)
        ->assertSee('Words that<br><em>matter.</em>', false)
        ->assertSee('Oluwafemi Oluwole')
        ->assertSee('Oracode Limited')
        ->assertSee('Joy Ugoyah')
        ->assertSee('Stavmia Technologies')
        ->assertSee('Chirdan John')
        ->assertSee('Homafella Realty');

    $this->get('/testimonial.html')->assertRedirect('/#testimonials');
});
