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

namespace Chevere\Examples;

use Chevere\Interfaces\Plugs\EventListener\EventListenerInterface;
use function Chevere\Components\Writers\writers;

final class HelloWorldEvent implements EventListenerInterface
{
    public function __invoke(array $data): void
    {
        writers()->out()->write('event:greetSet ' . implode(' ', $data));
    }

    public function anchor(): string
    {
        return 'greetSet';
    }

    public function at(): string
    {
        return EventHelloWorldController::class;
    }

    public function priority(): int
    {
        return 0;
    }
}
