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

namespace Chevere\Interfaces\Pluggable;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Countable;
use Traversable;

/**
 * Describes the component in charge of mapping plugs in the file system.
 */
interface PlugsMapInterface extends Countable
{
    public function __construct(PlugTypeInterface $type);

    /**
     * Provides access to the plugs type instance.
     */
    public function plugType(): PlugTypeInterface;

    /**
     * Return an instance with the specified added `$plug`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added `$plug`.
     *
     * @throws InvalidArgumentException
     * @throws OverflowException
     */
    public function withAdded(PlugInterface $plug): self;

    /**
     * Indicates whether the instance has the given `$plug`.
     */
    public function has(PlugInterface $plug): bool;

    /**
     * Indicates whether the instance has plugs for the given `$pluggable`.
     */
    public function hasPlugsFor(string $pluggable): bool;

    /**
     * Return the plugs queue typed for the given `$pluggable`.
     *
     * @throws OutOfBoundsException
     */
    public function getPlugsQueueTypedFor(string $pluggable): PlugsQueueTypedInterface;

    /**
     * @return Traversable<string, PlugsQueueTypedInterface>
     */
    public function getIterator(): Traversable;
}
