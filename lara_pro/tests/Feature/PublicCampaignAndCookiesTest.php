<?php

use App\Models\PromotionSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(fn () => Carbon::setTestNow());

it('shows cookie choices and no anniversary tag when no campaign is active', function () {
    Carbon::setTestNow('2026-07-01 12:00:00');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-cookie-prompt', false)
        ->assertSee('data-cookie-choice="essential"', false)
        ->assertSee('data-cookie-choice="accepted"', false)
        ->assertSee('Cookie preferences')
        ->assertDontSee('ed-promo tt-hero__anniversary tt-reveal', false);
});

it('shows the anniversary tag only while an enabled campaign is live', function () {
    Carbon::setTestNow('2026-07-01 12:00:00');
    $promotion = PromotionSetting::query()->create([
        'key' => 'anniversary',
        'enabled' => true,
        'years' => 7,
        'discount_percent' => 50,
        'promo_code' => 'TURANCE7',
        'ends_at' => '2026-07-31 23:59:59',
    ]);

    $this->get(route('home'))->assertOk()->assertSee('ed-promo tt-hero__anniversary tt-reveal', false);

    $promotion->update(['enabled' => false]);
    $this->get(route('home'))->assertOk()->assertDontSee('ed-promo tt-hero__anniversary tt-reveal', false);
});
