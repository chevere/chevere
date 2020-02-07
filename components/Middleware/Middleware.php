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

namespace Chevere\Components\Middleware;

use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareInterface;

abstract class Middleware implements MiddlewareInterface
{
    /**
     * Dummy method to avoid constructors as a new MiddlewareInterface is created in MiddlewareRunner.
     */
    final public function __construct()
    {
    }

    abstract public function handle(RequestInterface $request): void;
}
