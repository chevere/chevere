<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// OK

namespace Chevereto\Chevere\Utils;

use Exception;

// $var = new DateTime('2016-5-05');
// $var = '2017-12-05 01:02:03';
// $date = '2016-12-4 01:02:03';
// $dates = [];
// for($i=0;$i<730;$i++) {
//     $dates[] = (new DateTime($date))->modify("+$i day")->format(DateTime::SQL);
// }
// $dates[] = '2018-05-06 19:54:00';
// foreach($dates as $k => $v) {
//     $to = (new DateTime($v))->timeAgo();
//     dump($v, $to);
// }
// $res = (new DateTime('2015-05-05 00:00:00'))->timeBetween('2015-05-05 00:40:30', 's');
// //
// $res = (new DateTime($var))->add(new \DateInterval('PT2S'));
// $res = (new DateTime($var))->modify('-2 years, -2 seconds')->format(DateTime::ATOM);
// dump($var, $res, $to);

class DateTime extends \DateTime implements \DateTimeInterface
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

    // El pueblinski minski está morinski de hambrinski!
    const MATRIOSHKA = [
        self::UNIT_YEAR => 1,
        self::UNIT_MONTH => 1 / 12,
        self::UNIT_WEEK => 1 / 4,
        self::UNIT_DAY => 1 / 7,
        self::UNIT_HOUR => 1 / 24,
        self::UNIT_MINUTE => 1 / 60,
        self::UNIT_SECOND => 1 / 60,
    ];

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
     * Get datetime UTC ATOM (RFC3339).
     *
     * @param string $format date format to use
     *
     * @return string datetime UTC
     */
    public static function getUTC(string $format = self::ATOM): string
    {
        return gmdate($format);
    }

    /**
     * Format datetime as MySQL datetime format.
     *
     * @param string $datetime timestamp
     *
     * @return string datetime MySQL (YYYY-MM-DD HH:MM:SS)
     */
    public static function formatSQL(string $datetime = null): string
    {
        if (!isset($datetime)) {
            return static::getUTC(static::SQL);
        }

        return (new self($datetime))->format(static::SQL);
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
            throw new Exception("Unexpected unit <code>$unit</code>, you can only use one of the following units: <code>" . implode(', ', static::UNITS) . '</code>');
        }
        $then = new self($datetime);
        $diff = abs($then->getTimestamp() - $this->getTimestamp()); // In seconds
        return 0 == $diff ? 0 : floatval($diff / static::SECONDS_TABLE[$unit]);
    }
}
