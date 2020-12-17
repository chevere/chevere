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

namespace Chevere\Interfaces\Plugin;

use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Plugin\PlugInterfaceException;
use Chevere\Interfaces\To\ToArrayInterface;

/**
 * Describes the component in charge of defining a generic plugs queue.
 */
interface PlugsQueueInterface extends ToArrayInterface
{
    public function __construct(PlugTypeInterface $plugType);

    /**
     * Return an instance with the specified `$plug`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$plug`.
     *
     * @throws PlugInterfaceException
     * @throws OverflowException
     */
    public function withAdded(PlugInterface $plug): self;

    /**
     * Provides access to the plug type instance.
     */
    public function plugType(): PlugTypeInterface;

    /**
     * ```php
     * return [
     *     'for' => [0 => 'plugName',],
     * ]
     * ```
     */
    public function toArray(): array;
}
