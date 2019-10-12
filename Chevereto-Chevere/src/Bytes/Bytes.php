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

namespace Chevere\Bytes;

use Chevere\Message\Message;
use LogicException;
use PHPUnit\Framework\Constraint\LogicalOr;

final class Bytes
{
    const BYTES_UNITS = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    /** @var int */
    private $bytes;

    /** @var int */
    private $precision;

    public function __construct(int $bytes)
    {
        $this->bytes = $bytes;
        $this->precision = 0;
    }

    public function withPrecision(int $precision): Bytes
    {
        $new = clone $this;
        $new->precision = $precision;

        return $new;
    }

    /**
     * Converts bytes to MB.
     *
     * @param int $bytes bytes to be formatted
     *
     * @return float MB representation
     */
    public function toMB(): float
    {
        return round($this->bytes / pow(10, 6), $this->precision);
    }

    /**
     * Converts bytes to human readable representation.
     *
     * @param int    $round how many decimals you want to get, default 1
     *
     * @return string formatted size string like 10 MB
     */
    function toHumanReadable(): string
    {
        if ($this->bytes < 1000) {
            return $this->bytes . ' B';
        }
        foreach (static::BYTES_UNITS as $k => $v) {
            $multiplier = pow(1000, $k + 1);
            $threshold = $multiplier * 1000;
            if ($this->bytes < $threshold) {
                $size = round($this->bytes / $multiplier, $this->precision);

                return "$size $v";
            }
        }
        throw new LogicException(
            (new Message("Out of range, %bytes% bytes can't be converted"))
                ->code('%bytes%', $this->bytes)
        );
    }
}
