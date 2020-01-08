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
use Chevere\Components\Middleware\Exceptions\MiddlewareContractException;

interface MiddlewareNameContract
{
    /**
     * Creates a new instance.
     *
     * @param string $name A middleware name implementing the MiddlewareContract
     *
     * @throws InvalidArgumentException    if $name represents non existent class
     * @throws MiddlewareContractException if the $name doesn't represent a class implementing the MiddlewareContract
     */
    public function __construct(string $name);

    /**
     * Provides access to the middleware name.
     */
    public function toString(): string;
}
