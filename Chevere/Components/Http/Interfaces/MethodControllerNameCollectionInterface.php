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

namespace Chevere\Components\Http\Interfaces;

use Chevere\Components\Common\Interfaces\ToArrayInterface;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;

interface MethodControllerNameCollectionInterface extends ToArrayInterface
{
    public function __construct(MethodControllerNameInterface ...$methodControllerName);

    /**
     * Return an instance with the specified MethodControllerNameInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified MethodControllerNameInterface.
     */
    public function withAddedMethodControllerName(MethodControllerNameInterface $methodControllerName): MethodControllerNameCollectionInterface;

    /**
     * Returns a boolean indicating whether the instance has any MethodInterface.
     */
    public function hasAny(): bool;

    /**
     * Returns a boolean indicating whether the instance has the given MethodInterface.
     */
    public function has(MethodInterface $method): bool;

    /**
     * @throws MethodNotFoundException
     */
    public function get(MethodInterface $method): MethodControllerNameInterface;

    /**
     * @return array MethodControllerNameInterface[]
     */
    public function toArray(): array;
}
