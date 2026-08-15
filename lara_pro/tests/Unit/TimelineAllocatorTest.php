<?php

use App\Support\TimelineAllocator;

it('labels fixed timelines sequentially across line items', function () {
    expect(TimelineAllocator::forItems('6 weeks', 3))
        ->toBe(['1st week', '2nd week', '3rd week']);
});

it('labels timeline ranges sequentially across line items', function () {
    expect(TimelineAllocator::forItems('2 weeks to 3 weeks', 3))
        ->toBe([
            '1st week',
            '2nd week',
            '3rd week',
        ]);
});

it('preserves unsupported free-form timelines', function () {
    expect(TimelineAllocator::forItems('After final approval', 2))
        ->toBe(['After final approval', 'After final approval']);
});
