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

namespace Chevere\Interfaces\Pluggable\Plugs\EventListener;

use Chevere\Components\Pluggable\Plugs\EventListeners\EventListenersQueue;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Writer\WritersInterface;

/**
 * Describes the component in charge of running the event listeners queue.
 */
interface EventListenersRunnerInterface
{
    public function __construct(EventListenersQueue $queue, WritersInterface $writers);

    /**
     * Run registered event listeners for the target event anchor.
     *
     * @throws RuntimeException
     */
    public function run(string $anchor, array $data): void;
}
