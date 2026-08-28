<?php

use App\Support\StaffDailyMotivation;
use Carbon\Carbon;

it('changes the motivation greeting and quote by time of day', function () {
    $morning = StaffDailyMotivation::forDate(Carbon::parse('2026-08-27 09:00:00'));
    $afternoon = StaffDailyMotivation::forDate(Carbon::parse('2026-08-27 14:00:00'));
    $evening = StaffDailyMotivation::forDate(Carbon::parse('2026-08-27 19:00:00'));

    expect($morning['greeting'])->toBe('Good morning')
        ->and($afternoon['greeting'])->toBe('Good afternoon')
        ->and($evening['greeting'])->toBe('Good evening')
        ->and($morning['quote'])->not->toBe($afternoon['quote'])
        ->and($afternoon['quote'])->not->toBe($evening['quote']);
});

it('keeps a motivation stable within the same time period', function () {
    $first = StaffDailyMotivation::forDate(Carbon::parse('2026-08-27 09:00:00'));
    $second = StaffDailyMotivation::forDate(Carbon::parse('2026-08-27 11:59:59'));

    expect($first)->toBe($second);
});
