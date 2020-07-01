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

namespace Chevere\Interfaces\Route;

use Chevere\Exceptions\Regex\RegexException;

interface RouteWildcardMatchInterface
{
    /**
     * @param string $match Regex match (without delimiters, without starting ^ or ending $).
     * @throws RegexException If $match is an invalid regex matcher
     */
    public function __construct(string $match);

    /**
     * @return string Regex match (without delimiters).
     */
    public function toString(): string;
}
