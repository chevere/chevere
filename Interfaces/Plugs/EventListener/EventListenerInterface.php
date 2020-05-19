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

use Chevere\Interfaces\Plugin\PlugInterface;

interface EventListenerInterface extends PlugInterface
{
    public function __invoke(string $anchor, EventInterface $event): void;

    /**
     * @return string Applicable event anchor.
     */
    public function anchor(): string;

    /**
     * @return string Target class name implementing EventsInterface.
     */
    public function at(): string;

    /**
     * @return String Priority order.
     */
    public function priority(): int;
}
