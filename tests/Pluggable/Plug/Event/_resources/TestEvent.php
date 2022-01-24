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

namespace Chevere\Tests\Pluggable\Plug\Event\_resources;

use Chevere\Pluggable\Interfaces\Plug\Event\EventInterface;
use Chevere\Writer\Interfaces\WritersInterface;

final class TestEvent implements EventInterface
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
