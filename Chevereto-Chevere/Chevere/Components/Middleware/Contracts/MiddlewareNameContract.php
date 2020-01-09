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

namespace  Chevere\Components\Middleware\Contracts;

use InvalidArgumentException;
use Chevere\Components\Common\Contracts\ToStringContract;
use Chevere\Components\Middleware\Exceptions\MiddlewareContractException;

interface MiddlewareNameContract extends ToStringContract
{
    public function __construct(string $name);

    /**
     * @return string Middleware name.
     */
    public function toString(): string;
}
