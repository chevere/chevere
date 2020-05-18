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

use Chevere\Components\Plugin\Interfaces\PluggableAnchorsInterface;

interface EventsInterface
{
    /**
     * Returns the declared event names.
     */
    public static function getEventsAnchors(): PluggableAnchorsInterface;

    /**
     * Attach the events runner.
     */
    public function withEventsRunner(EventsRunnerInterface $runner): EventsInterface;

    /**
     * Run events for the given anchor (if-any).
     *
     * @param string $anchor Event anchor
     */
    public function sendEvent(string $anchor, EventInterface $event): void;
}
