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

namespace Chevere\Contracts\Http;

interface MethodControllerNameCollectionContract
{
    public function __construct(MethodControllerNameContract ...$methodControllerName);

    public function withAddedMethodControllerName(MethodControllerNameContract $methodControllerName): MethodControllerNameCollectionContract;

    /**
     * Returns a boolean indicating whether the instance has any MethodContract.
     */
    public function hasAny(): bool;

    /**
     * Returns a boolean indicating whether the instance has the given MethodContract.
     */
    public function has(MethodContract $method): bool;

    /**
     * @throws MethodNotFoundException
     */
    public function get(MethodContract $method): MethodControllerNameContract;

    public function toArray(): array;
}
