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

namespace Chevere\Components\Pluggable\Traits;

use Chevere\Components\Pluggable\PlugsQueue;
use Chevere\Interfaces\Pluggable\PlugInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueTypedInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;

trait TypedPlugsQueueTrait
{
    private PlugsQueueInterface $plugsQueue;

    public function __construct()
    {
        $this->plugsQueue = new PlugsQueue($this->getPlugType());
    }

    public function withAdded(PlugInterface $plug): PlugsQueueTypedInterface
    {
        $new = clone $this;
        $new->plugsQueue = $new->plugsQueue->withAdded($plug);

        return $new;
    }

    abstract public function interface(): string;

    abstract public function getPlugType(): PlugTypeInterface;

    public function plugsQueue(): PlugsQueueInterface
    {
        return $this->plugsQueue;
    }
}
