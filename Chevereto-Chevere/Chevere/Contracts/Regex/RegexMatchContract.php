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

namespace Chevere\Contracts\Regex;

use Chevere\Components\Regex\Exceptions\RegexException;

interface RegexMatchContract
{
    /**
     * Creates a new instance.
     *
     * @throws RegexException if $match is an invalid regex matcher
     */
    public function __construct(string $match);

    /**
     * Provides access to the match string.
     */
    public function toString(): string;
}
