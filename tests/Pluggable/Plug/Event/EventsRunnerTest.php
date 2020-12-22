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

use Chevere\Components\Pluggable\Plug\Event\EventsQueue;
use Chevere\Components\Pluggable\Plug\Event\EventsRunner;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Components\Writer\Writers;
use Chevere\Tests\Pluggable\Plug\Event\_resources\TestEvent;
use Laminas\Diactoros\StreamFactory;
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
        $stream = (new StreamFactory())->createStream('');
        $writer = new StreamWriter($stream);
        $writers = (new Writers())->with($writer);
        $event = new TestEvent();
        $eventsQueue = (new EventsQueue())
            ->withAdded($event);
        $runner = new EventsRunner($eventsQueue, $writers);
        $data = ['data'];
        $runner->run($event->anchor(), $data);
        $this->assertSame(implode(' ', $data), $writer->toString());
    }
}
