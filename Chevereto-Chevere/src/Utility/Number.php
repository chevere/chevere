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

namespace Chevereto\Chevere\Utility;

abstract class Number
{
    /**
     * Abbreviate a number adding its alpha suffix.
     *
     * @param mixed $number    number to be abbreviated
     * @param int   $precision round precision
     *
     * @return string Abbreviated number (ie. 2K or 1M).
     */
    public static function abbreviate(int $number, int $precision = 0): ?string
    {
        if ($number != 0) {
            $abbreviations = [
                24 => 'Y',
                21 => 'Z',
                18 => 'E',
                15 => 'P',
                12 => 'T',
                9 => 'B',
                6 => 'M',
                3 => 'K',
                0 => null,
            ];
            foreach ($abbreviations as $exponent => $abbreviation) {
                if ($number >= pow(10, $exponent)) {
                    $div = $number / pow(10, $exponent);
                    $float = floatval($div);
                    $number = null === $abbreviation ? (string) $float : (round($float, $precision).$abbreviation);
                    break;
                }
            }
        }

        return (string) $number;
    }

    /**
     * Converts a fraction into a decimal (float).
     *
     * @param string $fraction a fraction number (like 1/25)
     */
    public static function fractionToDecimal($fraction): ?float
    {
        [$top, $bottom] = explode('/', $fraction);

        return (float) ($bottom == 0 ? $fraction : ($top / $bottom));
    }
}
