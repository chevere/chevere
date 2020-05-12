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

namespace Chevere\Components\Plugs\Interfaces;

interface PlugsQueueInterface
{
    public function __construct(PlugTypeInterface $plugType);

    public function withAddedPlug(PlugInterface $plug): PlugsQueueInterface;

    public function plugType(): PlugTypeInterface;

    /**
     * @return array [for => [priority => plugName,],]
     */
    public function toArray(): array;
}
