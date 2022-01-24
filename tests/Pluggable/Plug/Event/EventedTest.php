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
use Chevere\Tests\Pluggable\Plug\Event\_resources\TestEventable;
use PHPUnit\Framework\TestCase;

final class EventedTest extends TestCase
{
    public function testWithoutEventsQueue(): void
    {
        $string = 'string';
        $testEventable = new TestEventable();
        $testEventable->setString($string);
        $this->assertSame($string, $testEventable->string());
    }

    public function testEvents(): void
    {
        $writer = new StreamWriter(streamTemp(''));
        $writers = (new Writers())->with($writer);
        $string = 'string';
        $eventsQueue = (new EventsQueue())->withAdded(new TestEvent());
        /** @var TestEventable $testEventable */
        $testEventable = (new TestEventable())
            ->withEventsRunner(
                new EventsRunner($eventsQueue, $writers)
            );
        $testEventable->setString($string);
        $this->assertSame($writers->output()->__toString(), implode(' ', [$string]));
    }

    public function testNotEventedClass(): void
    {
        $string = 'string';
        $testEventable = new TestEventable();
        $testEventable->setString($string);
        $this->assertSame($string, $testEventable->string());
    }
}
