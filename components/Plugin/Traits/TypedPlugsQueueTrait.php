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
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;

trait TypedPlugsQueueTrait
{
    private PlugsQueueInterface $queue;

    public function __construct()
    {
        $this->queue = new PlugsQueue($this->getPlugType());
    }

    /**
     * @return string The accepted plug interface.
     */
    abstract public function accept(): string;

    abstract public function getPlugType(): PlugTypeInterface;

    public function queue(): PlugsQueueInterface
    {
        return $this->queue;
    }
}
