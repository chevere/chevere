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

namespace Chevere\Pluggable\Interfaces;

/**
 * Describes the base interface used by typed plug queues.
 */
interface PlugsQueueTypedInterface
{
    public function withAdded(PlugInterface $plug): self;

    /**
     * Returns the interface name of plug members.
     */
    public function interface(): string;

    /**
     * Returns a new instance for the members plug type.
     */
    public function getPlugType(): PlugTypeInterface;

    /**
     * Provides access to the plugs queue instance.
     */
    public function plugsQueue(): PlugsQueueInterface;
}
