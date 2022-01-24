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

namespace Chevere\Spec\Interfaces;

use Chevere\DataStructure\Interfaces\MappedInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Iterator;

/**
 * Describes the component in charge of collecting route spec endpoints.
 */
interface SpecIndexMapInterface extends MappedInterface
{
    /**
     * Return an instance with the specified `$specEndpoints` for `$routeName`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$specEndpoints` for `$routeName`.
     */
    public function withPut(string $routeName, SpecEndpointsInterface $specEndpoints): self;

    /**
     * Indicates whether the instance has a spec endpoints identified by `$routeName`.
     */
    public function hasKey(string $routeName): bool;

    /**
     * Returns the route endpoint spec identified by its `$key`.
     * @throws OutOfBoundsException
     */
    public function get(string $routeName): SpecEndpointsInterface;

    /**
     * @return Iterator<string, SpecEndpointsInterface>
     */
    public function getIterator(): Iterator;
}
