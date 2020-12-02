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

namespace Chevere\Components\Plugin\Traits;

use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Interfaces\Plugin\PlugInterface;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Chevere\Interfaces\Plugin\PlugsQueueTypedInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;

trait TypedPlugsQueueTrait
{
    private PlugsQueueInterface $plugsQueue;

    public function __construct()
    {
        $this->plugsQueue = new PlugsQueue($this->getPlugType());
    }

    public function withAdded(PlugInterface $plug): PlugsQueueTypedInterface
    {
        /**
         * @var PlugsQueueTypedInterface $new
         */
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
