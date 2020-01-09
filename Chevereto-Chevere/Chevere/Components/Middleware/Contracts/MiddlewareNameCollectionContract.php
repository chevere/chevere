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

use Chevere\Components\Common\Contracts\ToArrayContract;

interface MiddlewareNameCollectionContract extends ToArrayContract
{
    public function __construct(MiddlewareNameContract ...$middlewareNames);

    /**
     * Return an instance with the specified middleware name.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified middleware name.
     *
     * @throws MiddlewareContractException if $name doesn't represent a class implementing the MiddlewareContract
     */
    public function withAddedMiddlewareName(MiddlewareNameContract $middlewareName): MiddlewareNameCollectionContract;

    /**
     * Returns a boolean indicating whether the instance has any MiddlewareContract.
     */
    public function hasAny(): bool;

    /**
     * Returns a boolean indicating whether the instance has the given MiddlewareNameContract.
     */
    public function has(MiddlewareNameContract $middlewareName): bool;

    /**
     * @return array MiddlewareNameContract[]
     */
    public function toArray(): array;
}
