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

interface RegexContract extends ToStringContract
{
    /**
     * Creates a new instance.
     *
     * @throws RegexException if $regex is not a valid regular expresion
     */
    public function __construct(string $regex);

    /**
     * @return string Regex
     */
    public function toString(): string;
}
