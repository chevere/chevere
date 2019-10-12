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

namespace Chevere\Number;

final class Number
{
    /** @var int */
    private $number;

    /** @var int */
    private $precision;

    public function __construct(int $number)
    {
        $this->number = $number;
        $this->precision = 0;
    }

    public function withPrecision(int $precision)
    {
        $new = clone $this;
        $new->precision = $precision;

        return $new;
    }
    /**
     * Abbreviate a number adding its alpha suffix.
     *
     * @return string Abbreviated number (ie. 2K or 1M).
     */
    public function toAbbreviate(): string
    {
        /** @var string */
        $string = (string) $this->number;
        if ($this->number != 0) {
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
                if ($this->number >= pow(10, $exponent)) {
                    $div = $this->number / pow(10, $exponent);
                    $float = floatval($div);
                    $string = (string) (null === $abbreviation
                        ? $float
                        : (round($float, $this->precision) . $abbreviation));
                    break;
                }
            }
        }

        return $string;
    }

    /**
     * Converts a fraction into a decimal (float).
     *
     * @param string $fraction a fraction number (like 1/25)
     */
    // public static function fractionToDecimal($fraction): ?float
    // {
    //     [$top, $bottom] = explode('/', $fraction);

    //     return (float) ($bottom == 0 ? $fraction : ($top / $bottom));
    // }
}
