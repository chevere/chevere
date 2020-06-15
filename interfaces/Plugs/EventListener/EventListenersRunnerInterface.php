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

namespace Chevere\Interfaces\Plugs\EventListener;

use Chevere\Components\Plugin\Plugs\EventListeners\EventListenersQueue;
use Chevere\Interfaces\Writers\WritersInterface;

interface EventListenersRunnerInterface
{
    public function __construct(EventListenersQueue $queue, WritersInterface $writers);

    /**
     * Run registered event listeners for the target event name.
     */
    public function run(string $name, array $data): void;
}
