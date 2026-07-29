<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Deliberately hand-rolled rather than Carbon's diffForHumans: the two ports
 * must print the same string for the same instant, and the stock helpers
 * disagree (one rounds, the other truncates). Floor every unit, no fuzzy prefix.
 *
 * Mirrors ApplicationHelper#relative_time in the Rails app.
 */
class RelativeTime
{
    public static function for(CarbonInterface $time): string
    {
        $seconds = max(0, now()->getTimestamp() - $time->getTimestamp());

        if ($seconds < 60) {
            return 'just now';
        }

        $minutes = intdiv($seconds, 60);
        if ($minutes < 60) {
            return self::unit($minutes, 'minute');
        }

        $hours = intdiv($minutes, 60);
        if ($hours < 24) {
            return self::unit($hours, 'hour');
        }

        $days = intdiv($hours, 24);
        if ($days < 30) {
            return self::unit($days, 'day');
        }

        $months = intdiv($days, 30);
        if ($months < 12) {
            return self::unit($months, 'month');
        }

        return self::unit(intdiv($days, 365), 'year');
    }

    private static function unit(int $count, string $unit): string
    {
        return $count.' '.$unit.($count === 1 ? '' : 's').' ago';
    }
}
