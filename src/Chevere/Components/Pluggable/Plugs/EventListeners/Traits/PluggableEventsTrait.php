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

namespace Chevere\Components\Pluggable\Plugs\EventListeners\Traits;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\EventListenersRunnerInterface;
use Chevere\Interfaces\Pluggable\Plugs\EventListener\PluggableEventsInterface;

trait PluggableEventsTrait
{
    private EventListenersRunnerInterface $eventsRunner;

    public function withEventListenersRunner(EventListenersRunnerInterface $eventsRunner): PluggableEventsInterface
    {
        if (! ($this instanceof PluggableEventsInterface)) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message('This method applies only for %interface%'))
                    ->code('%interface%', PluggableEventsInterface::class)
            );
            // @codeCoverageIgnoreEnd
        }
        $new = clone $this;
        $new->eventsRunner = $eventsRunner;
        /**
         * @var PluggableEventsInterface $new
         */
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
