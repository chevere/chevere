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

namespace Chevere\Components\Str\Interfaces;

interface StrInterface
{
    public function __construct(string $string);

    public function __toString(): string;

    /**
     * Lowercase string
     */
    public function lowercase(): StrInterface;

    /**
     * Strip whitespaces from string.
     */
    public function stripWhitespace(): StrInterface;

    /**
     * Strip extra whitespace from string.
     */
    public function stripExtraWhitespace(): StrInterface;

    /**
     * Strip non-alphanumeric chars from string.
     */
    public function stripNonAlphanumerics(): StrInterface;

    /**
     * Converts backslash into forward slashes.
     */
    public function forwardSlashes(): StrInterface;
}
