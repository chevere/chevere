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

namespace Chevere\Components\Pluggable\Plug\Event\Traits;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Pluggable\Plug\Event\EventsRunnerInterface;
use Chevere\Interfaces\Pluggable\Plug\Event\PluggableEventsInterface;

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
