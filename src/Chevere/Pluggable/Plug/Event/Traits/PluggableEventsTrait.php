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

namespace Chevere\Pluggable\Plug\Event\Traits;

use Chevere\Message\Message;
use Chevere\Pluggable\Interfaces\Plug\Event\EventsRunnerInterface;
use Chevere\Pluggable\Interfaces\Plug\Event\PluggableEventsInterface;
use Chevere\Throwable\Exceptions\LogicException;

trait PluggableEventsTrait
{
    private EventsRunnerInterface $eventsRunner;

    public function withEventsRunner(EventsRunnerInterface $eventsRunner): static
    {
        if (!($this instanceof PluggableEventsInterface)) {
            // @codeCoverageIgnoreStart
            throw new LogicException(
                (new Message('This method applies only for %interface%'))
                    ->code('%interface%', PluggableEventsInterface::class)
            );
            // @codeCoverageIgnoreEnd
        }
        $new = clone $this;
        $new->eventsRunner = $eventsRunner;

        return $new;
    }

    public function event(string $anchor, array $data = []): void
    {
        if (!isset($this->eventsRunner)) {
            return;
        }
        $this->eventsRunner->run($anchor, $data);
    }
}
