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

namespace Chevere\Components\Regex\Contracts;

use Chevere\Components\Common\Contracts\ToStringContract;
use Chevere\Components\Regex\Exceptions\RegexException;

interface RegexMatchContract extends ToStringContract
{
    /**
     * Creates a new instance.
     *
     * @throws RegexException if $match is an invalid regex matcher
     */
    public function __construct(string $match);

    /**
     * @return string Match.
     */
    public function toString(): string;
}
