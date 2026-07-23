<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response
        ->assertStatus(200)
        ->assertSee('Excellence')
        ->assertSee('Delivered')
        ->assertSee('turance-gold-sculpture.webp', false)
        ->assertDontSee('Web Authority Sharper')
        ->assertDontSee('home-luxury');
});
