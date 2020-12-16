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

namespace Chevere\Interfaces\Router\Routing;

use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Countable;
use Generator;

/**
 * Describes the component in charge of collecting objects implementing `RoutingDescriptorInterface`.
 */
interface RoutingDescriptorsInterface extends Countable
{
    /**
     * Return an instance with the specified `$descriptor` added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$descriptor` added.
     *
     * @throws OverflowException
     */
    public function withAdded(RoutingDescriptorInterface $descriptor): RoutingDescriptorsInterface;

    /**
     * Indicates whether the instance has the given `$descriptor`.
     */
    public function contains(RoutingDescriptorInterface $descriptor): bool;

    /**
     * @throws OutOfBoundsException
     */
    public function get(int $position): RoutingDescriptorInterface;

    /**
     * @return Generator<int, RoutingDescriptorInterface>
     */
    public function getGenerator(): Generator;
}
