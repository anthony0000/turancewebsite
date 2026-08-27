<?php

namespace App\Support;

use Carbon\CarbonInterface;

final class StaffDailyMotivation
{
    private const QUOTES = [
        'Momentum is built one finished task at a time.',
        'Small, focused progress turns a busy board into a finished project.',
        'The detail you handle today is the confidence the team carries tomorrow.',
        'Good work becomes great work when it moves the whole team forward.',
        'You do not need a perfect day. You only need a clear next step.',
        'Every task you close makes room for better work.',
        'Consistency is a quiet kind of excellence.',
        'Make the next useful move. Momentum will meet you there.',
    ];

    public static function forDate(?CarbonInterface $date = null): array
    {
        $date ??= now();
        $quote = self::QUOTES[abs(crc32($date->toDateString())) % count(self::QUOTES)];

        return [
            'quote' => $quote,
            'attribution' => 'Your Turance team',
            'date' => $date->format('l, F j'),
        ];
    }
}
