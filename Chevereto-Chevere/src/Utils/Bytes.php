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

namespace Chevereto\Chevere\Utils;

abstract class Bytes
{
    const UNITS = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    /**
     * Returns bytes from size + suffix format.
     *
     * @param string $size bytes to be formatted, like "2 MB"
     * @param int    $cut  indicates the chop needed to substract the byte-unit
     *
     * @return int byte representation
     */
    public static function get(string $size, int $cut = -2): int
    {
        $suffix = strtoupper(substr($size, $cut));
        if (strlen($suffix) == 1) {
            $iniToSuffix = ['M' => 'MB', 'G' => 'GB'];
            $suffix = $iniToSuffix[$suffix];
        }
        $value = intval($size);
        if (!in_array($suffix, static::UNITS)) {
            return $value;
        }

        $array_search = array_search($suffix, static::UNITS);
        if (false !== $array_search) {
            $powFactor = (int) $array_search + 1;
        } else {
            $powFactor = 0;
        }

        return (int) ($value * pow(1000, $powFactor));
    }

    /**
     * Converts bytes to human readable representation.
     *
     * @param string $bytes bytes to be formatted
     * @param int    $round how many decimals you want to get, default 1
     *
     * @return string formatted size string like 10 MB
     */
    public static function format($bytes, $round = 1): ?string
    {
        if (!is_numeric($bytes)) {
            return null;
        }
        if ($bytes < 1000) {
            return "$bytes B";
        }
        foreach (static::UNITS as $k => $v) {
            $multiplier = pow(1000, $k + 1);
            $threshold = $multiplier * 1000;
            if ($bytes < $threshold) {
                $size = round($bytes / $multiplier, $round);

                return "$size $v";
            }
        }
    }

    /**
     * Converts bytes to MB.
     *
     * @param int $bytes bytes to be formatted
     *
     * @return float MB representation
     */
    public static function toMB(int $bytes): float
    {
        return round($bytes / pow(10, 6));
    }

    /**
     * Get bytes from the php.ini values.
     *
     * Allows to get a byte representation from php.ini values which uses short format notation.
     *
     * @param string $size short size notation (like "2M")
     *
     * @return float byte representation
     */
    public static function getIni(string $size): float
    {
        return static::get($size, -1);
    }
}
