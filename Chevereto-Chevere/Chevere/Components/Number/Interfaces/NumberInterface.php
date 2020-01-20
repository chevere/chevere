<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Number\Interfaces;

interface NumberInterface
{
    public function __construct($number);

    /**
     * Return an instance with the specified decimal precision.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified decimal precision.
     */
    public function withPrecision(int $precision): NumberInterface;

    /**
     * Provides access to the instance decimal precision.
     */
    public function precision(): int;

    /**
     * Abbreviate a number adding its alpha suffix.
     *
     * Note: It removes unnecesary zeroes (100.00M -> 100M) when using precision.
     *
     * @return string Abbreviated number (ie. 2K or 1M).
     */
    public function toAbbreviate(): string;
}
