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

namespace Chevere\Components\Plugs\EventListener\Interfaces;

interface EventsRunnerInterface
{
    public function __construct(EventsQueue $queue);

    /**
     * Run registered event listeneners for the target event name.
     */
    public function run(string $name, array $data = []): void;
}
