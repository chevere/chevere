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

namespace Chevere\Pluggable\Interfaces\Plug\Event;

use Chevere\Pluggable\Interfaces\PluggableAnchorsInterface;

/**
 * Describes the interface for components requiring to implement pluggable events.
 */
interface PluggableEventsInterface
{
    /**
     * Returns the declared event anchors able to plug.
     */
    public static function getEventAnchors(): PluggableAnchorsInterface;

    /**
     * Return an instance with the specified `$runner`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$runner`.
     *
     * This method should be implemented in the runtime strategy before running events.
     */
    public function withEventsRunner(EventsRunnerInterface $runner): self;

    /**
     * Run events for the given anchor (if-any).
     *
     * @param string $anchor Event anchor.
     * @param array $data Data to pass to the event listeners.
     */
    public function event(string $anchor, array $data = []): void;
}
