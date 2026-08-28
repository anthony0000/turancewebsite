<?php

namespace App\Support;

use Carbon\CarbonInterface;

final class StaffDailyMotivation
{
    private const QUOTES = [
        'morning' => [
            'Set the tone for a focused, productive day with one clear next step.',
            'A strong morning start turns today\'s priorities into tomorrow\'s progress.',
            'Begin with intention. Every finished task builds momentum for the team.',
        ],
        'afternoon' => [
            'Keep the momentum going. The progress you make now keeps the whole team moving.',
            'A focused afternoon can turn a busy board into a finished project.',
            'The detail you handle today becomes the confidence the team carries tomorrow.',
        ],
        'evening' => [
            'Close the day with purpose. Every task you finish makes room for better work.',
            'Take a moment to finish strong and leave tomorrow a clearer next step.',
            'Consistency is a quiet kind of excellence. Your progress today matters.',
        ],
    ];

    public static function forDate(?CarbonInterface $date = null): array
    {
        $date ??= now();
        $period = self::periodForHour((int) $date->format('G'));
        $quotes = self::QUOTES[$period];
        $quote = $quotes[abs(crc32($date->toDateString().':'.$period)) % count($quotes)];

        return [
            'quote' => $quote,
            'greeting' => match ($period) {
                'morning' => 'Good morning',
                'afternoon' => 'Good afternoon',
                default => 'Good evening',
            },
            'attribution' => 'Your Turance team',
            'date' => $date->format('l, F j'),
        ];
    }

    private static function periodForHour(int $hour): string
    {
        return match (true) {
            $hour < 12 => 'morning',
            $hour < 18 => 'afternoon',
            default => 'evening',
        };
    }
}
