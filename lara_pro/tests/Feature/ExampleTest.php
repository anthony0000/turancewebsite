<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $response
        ->assertStatus(200)
        ->assertSee('Digital experiences')
        ->assertSee('Start your project')
        ->assertSee('turance-editorial-reference.png', false)
        ->assertDontSee('Web Authority Sharper')
        ->assertDontSee('home-luxury');
});
