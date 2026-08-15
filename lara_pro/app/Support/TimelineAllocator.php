<?php

namespace App\Support;

final class TimelineAllocator
{
    /**
     * Assign sequential period labels to line items.
     *
     * Free-form timelines are returned unchanged so existing invoices keep
     * their original wording when no period unit can be identified safely.
     *
     * @return array<int, string>
     */
    public static function forItems(?string $timeline, int $itemCount): array
    {
        $timeline = trim((string) $timeline);
        $itemCount = max(1, $itemCount);

        if ($timeline === '') {
            return array_fill(0, $itemCount, '');
        }

        if ($itemCount === 1) {
            return [$timeline];
        }

        $unit = self::periodUnit($timeline);

        if ($unit === null) {
            return array_fill(0, $itemCount, $timeline);
        }

        return array_map(
            fn (int $index): string => self::ordinal($index + 1).' '.$unit,
            range(0, $itemCount - 1)
        );
    }

    private static function periodUnit(string $timeline): ?string
    {
        $periodPattern = '/(?:\d+(?:\.\d+)?)\s*(days?|weeks?|months?)/i';

        if (preg_match($periodPattern, $timeline, $matches) !== 1) {
            return null;
        }

        return rtrim(strtolower($matches[1]), 's');
    }

    private static function ordinal(int $value): string
    {
        $suffix = match (true) {
            $value % 100 >= 11 && $value % 100 <= 13 => 'th',
            $value % 10 === 1 => 'st',
            $value % 10 === 2 => 'nd',
            $value % 10 === 3 => 'rd',
            default => 'th',
        };

        return $value.$suffix;
    }
}
