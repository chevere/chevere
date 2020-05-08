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

namespace Chevere\Components\Events\Interfaces;

use Chevere\Components\Events\EventRunner;

interface EventableInterface
{
    /**
     * Returns the declared event names.
     */
    public static function getEventNames(): EventNames;

    /**
     * Attach the hooks runner for this hookable.
     */
    public function withEventsRunner(EventRunner $runner): EventableInterface;

    /**
     * Run events for the given anchor (if-any).
     *
     * @param string $anchor Event anchor
     */
    public function hook(string $anchor, array $data = []): void;
}
