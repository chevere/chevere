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

use Chevere\Interfaces\Plugin\PluggableAnchorsInterface;

interface PluggableEventsInterface
{
    /**
     * @return PluggableAnchorsInterface declared event anchors.
     */
    public static function getEventAnchors(): PluggableAnchorsInterface;

    /**
     * Attach the events runner.
     */
    public function withEventListenersRunner(EventListenersRunnerInterface $runner): PluggableEventsInterface;

    /**
     * Run events for the given anchor (if-any).
     *
     * @param string $anchor Event anchor
     */
    public function event(string $anchor, array $data = []): void;
}
