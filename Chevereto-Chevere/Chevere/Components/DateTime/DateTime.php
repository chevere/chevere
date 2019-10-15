<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\DateTime;

use DateTime as DateTimeBase;
use InvalidArgumentException;

// FIXME: Set the constants in the contract
final class DateTime extends DateTimeBase
{
    const UNIT_HOUR = 'h';
    const UNIT_MINUTE = 'i';
    const UNIT_SECOND = 's';
    const UNIT_DAY = 'd';
    const UNIT_WEEK = 'w';
    const UNIT_MONTH = 'm';
    const UNIT_YEAR = 'y';

    const UNITS = [
        self::UNIT_HOUR,
        self::UNIT_MINUTE,
        self::UNIT_SECOND,
        self::UNIT_DAY,
        self::UNIT_WEEK,
        self::UNIT_MONTH,
        self::UNIT_YEAR,
    ];

    const UNITS_READABLE = [
        self::UNIT_SECOND => 'second',
        self::UNIT_MINUTE => 'minute',
        self::UNIT_HOUR => 'hour',
        self::UNIT_DAY => 'day',
        self::UNIT_WEEK => 'week',
        self::UNIT_MONTH => 'month',
        self::UNIT_YEAR => 'year',
    ];

    const SQL = 'Y-m-d H:i:s';

    const MINUTE_SECONDS = 60;
    const HOUR_SECONDS = 3600;
    const DAY_SECONDS = 86400;
    const WEEK_SECONDS = 604800;
    const MONTH_SECONDS = 2629750;
    const YEAR_SECONDS = 31556900;

    const SECONDS_TABLE = [
        self::UNIT_SECOND => 1,
        self::UNIT_MINUTE => self::MINUTE_SECONDS,
        self::UNIT_HOUR => self::HOUR_SECONDS,
        self::UNIT_DAY => self::DAY_SECONDS,
        self::UNIT_WEEK => self::WEEK_SECONDS,
        self::UNIT_MONTH => self::MONTH_SECONDS,
        self::UNIT_YEAR => self::YEAR_SECONDS,
    ];

    /**
     * Get datetime UTC
     *
     * @param string $format date format to use
     *
     * @return string datetime UTC (defined by $format)
     */
    public static function getUtc(string $format): string
    {
        return gmdate($format);
    }

    /**
     * Get datetime UTC ATOM (RFC3339).
     *
     *
     * @return string datetime UTC ATOM RFC3339
     */
    public static function getUtcAtom(): string
    {
        return gmdate(self::ATOM);
    }

    /**
     * Returns the difference between two dates in the target time unit.
     * Useful to compare datetimes in a given unit.
     *
     * @param string $datetime datetime
     * @param string $unit     time unit (default 's') [s;i;h;d;w;m;y]
     *
     * @return float time diff between the two datetimes
     */
    public function timeBetween(string $datetime, string $unit = self::UNIT_SECOND): float
    {
        if (!isset(static::SECONDS_TABLE[$unit])) {
            throw new InvalidArgumentException("Unexpected unit <code>$unit</code>, you can only use one of the following units: <code>" . implode(', ', static::UNITS) . '</code>');
        }
        $then = new self($datetime);
        $diff = abs($then->getTimestamp() - $this->getTimestamp()); // In seconds
        return 0 == $diff ? 0 : floatval($diff / static::SECONDS_TABLE[$unit]);
    }
}
