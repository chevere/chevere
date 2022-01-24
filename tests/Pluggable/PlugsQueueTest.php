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

namespace Chevere\Tests\Pluggable;

use Chevere\Pluggable\PlugsQueue;
use Chevere\Pluggable\Types\EventPlugType;
use Chevere\Pluggable\Types\HookPlugType;
use Chevere\Tests\Pluggable\_resources\src\TestHook;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\OverflowException;
use PHPUnit\Framework\TestCase;

final class PlugsQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugType = new HookPlugType();
        $plugsQueue = new PlugsQueue($plugType);
        $this->assertSame($plugType, $plugsQueue->plugType());
        $this->assertSame([], $plugsQueue->toArray());
    }

    public function testWithWrongPlug(): void
    {
        $hook = new TestHook();
        $plugType = new EventPlugType();
        $plugsQueue = new PlugsQueue($plugType);
        $this->expectException(TypeError::class);
        $plugsQueue->withAdded($hook);
    }

    public function testWithPlug(): void
    {
        $hook = new TestHook();
        $plugQueue = new PlugsQueue(new HookPlugType());
        $plugQueueWithAdded = $plugQueue->withAdded($hook);
        $this->assertNotSame($plugQueue, $plugQueueWithAdded);
        $this->assertSame([
            $hook->anchor() => [
                0 => [
                    $hook::class,
                ],
            ],
        ], $plugQueueWithAdded->toArray());
    }

    public function testWithAlreadyAddedPlug(): void
    {
        $hook = new TestHook();
        $plugsQueue = (new PlugsQueue(new HookPlugType()))
            ->withAdded($hook);
        $this->expectException(OverflowException::class);
        $plugsQueue->withAdded($hook);
    }
}
