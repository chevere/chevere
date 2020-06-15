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

namespace Chevere\Components\Plugin\Plugs\EventListeners\Traits;

use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Plugin\Plugs\EventListener\EventListenersRunnerInterface;
use Chevere\Interfaces\Plugin\Plugs\EventListener\PluggableEventsInterface;

trait PluggableEventsTrait
{
    private EventListenersRunnerInterface $eventsRunner;

    public function withEventListenersRunner(EventListenersRunnerInterface $eventsRunner): PluggableEventsInterface
    {
        if (!($this instanceof PluggableEventsInterface)) {
            throw new LogicException; // @codeCoverageIgnore
        }
        /**
         * @var PluggableEventsInterface $new
         */
        $new = clone $this;
        $new->eventsRunner = $eventsRunner;

        return $new;
    }

    public function event(string $anchor, array $data = []): void
    {
        if (isset($this->eventsRunner) === false) {
            return;
        }
        $this->eventsRunner->run($anchor, $data);
    }
}
