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

namespace Chevere\Interfaces\Middleware;

use Chevere\Interfaces\DataStructures\DsMapInterface;
use Chevere\Interfaces\To\ToArrayInterface;
use Psr\Http\Server\MiddlewareInterface;

interface MiddlewaresInterface extends DsMapInterface
{
    /**
     * Return an instance with the specified $middleware.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified $middleware.
     *
     * @throws MiddlewareInterfaceException if $name doesn't represent a class implementing the MiddlewareInterface
     */
    public function withAddedMiddleware(MiddlewareInterface $middleware): MiddlewaresInterface;

    /**
     * Returns a boolean indicating whether the instance has the given MiddlewareNameInterface.
     */
    public function has(MiddlewareInterface $middleware): bool;

    /**
     * @return array MiddlewareInterface[]
     */
    // public function toArray(): array;
}
