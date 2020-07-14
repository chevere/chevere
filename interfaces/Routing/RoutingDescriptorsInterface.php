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

namespace Chevere\Interfaces\Routing;

use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Routing\RoutingDescriptorAlreadyAddedException;
use Countable;

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
     * @throws RoutingDescriptorAlreadyAddedException
     * @throws OverflowException
     */
    public function withAdded(RoutingDescriptorInterface $descriptor): RoutingDescriptorsInterface;

    /**
     * Provides access to the element count.
     */
    public function count(): int;

    /**
     * Indicates whether the instance has the given `$descriptor`.
     */
    public function has(RoutingDescriptorInterface $descriptor): bool;

    /**
     * @throws OutOfRangeException
     */
    public function get(int $position): RoutingDescriptorInterface;
}
