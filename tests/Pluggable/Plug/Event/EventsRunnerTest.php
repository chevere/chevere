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

namespace Chevere\Tests\Pluggable\Plug\Event;

use Chevere\Pluggable\Plug\Event\EventsQueue;
use Chevere\Pluggable\Plug\Event\EventsRunner;
use function Chevere\Writer\streamTemp;
use Chevere\Writer\StreamWriter;
use Chevere\Writer\Writers;
use Chevere\Tests\Pluggable\Plug\Event\_resources\TestEvent;
use PHPUnit\Framework\TestCase;

final class EventsRunnerTest extends TestCase
{
    public function testConstruct(): void
    {
        $runner = new EventsRunner(new EventsQueue(), new Writers());
        $this->expectNotToPerformAssertions();
        $runner->run('anchor', []);
    }

    public function testRun(): void
    {
        $writer = new StreamWriter(streamTemp(''));
        $writers = (new Writers())->with($writer);
        $event = new TestEvent();
        $eventsQueue = (new EventsQueue())
            ->withAdded($event);
        $runner = new EventsRunner($eventsQueue, $writers);
        $data = ['data'];
        $runner->run($event->anchor(), $data);
        $this->assertSame(implode(' ', $data), $writer->__toString());
    }
}
