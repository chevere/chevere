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

namespace  Chevere\Components\Middleware\Interfaces;

use Chevere\Components\Common\Interfaces\ToArrayInterface;

interface MiddlewareNameCollectionInterface extends ToArrayInterface
{
    public function __construct(MiddlewareNameInterface ...$middlewareNames);

    /**
     * Return an instance with the specified middleware name.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified middleware name.
     *
     * @throws MiddlewareInterfaceException if $name doesn't represent a class implementing the MiddlewareInterface
     */
    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): MiddlewareNameCollectionInterface;

    /**
     * Returns a boolean indicating whether the instance has any MiddlewareInterface.
     */
    public function hasAny(): bool;

    /**
     * Returns a boolean indicating whether the instance has the given MiddlewareNameInterface.
     */
    public function has(MiddlewareNameInterface $middlewareName): bool;

    /**
     * @return array MiddlewareNameInterface[]
     */
    public function toArray(): array;
}
