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

namespace Chevere\Components\Route\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface RouteWildcardMatchInterface extends ToStringInterface
{
    /**
     * @param string $match Regex match (without delimiters).
     * @throws RegexException If $match is an invalid regex matcher
     */
    public function __construct(string $match);

    /**
     * @return string Regex match (without delimiters).
     */
    public function toString(): string;
}
