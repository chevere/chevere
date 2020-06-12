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

namespace Chevere\Tests\Plugs\EventListeners\_resources;

use Chevere\Interfaces\Plugs\EventListener\EventListenerInterface;
use Chevere\Interfaces\Writers\WritersInterface;
use Chevere\Tests\Plugs\EventListeners\_resources\TestEventable;

final class TestEventListener implements EventListenerInterface
{
    public function __invoke(array $data, WritersInterface $writers): void
    {
        $writers->debug()->write(implode(' ', $data));
    }

    public function anchor(): string
    {
        return 'setString:after';
    }

    public function at(): string
    {
        return TestEventable::class;
    }

    public function priority(): int
    {
        return 0;
    }
}
