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

namespace Chevere\Components\Plugs\Tests;

use Chevere\Components\Hooks\HooksQueue;
use Chevere\Components\Hooks\Tests\_resources\TestHook;
use Chevere\Components\Plugs\PlugsQueue;
use LogicException;
use PHPUnit\Framework\TestCase;

final class HooksQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $hooksQueue = new HooksQueue;
        $this->assertSame([], $hooksQueue->toArray());
    }

    public function testWithHook(): void
    {
        $hook = new TestHook;
        $hooksQueue = new HooksQueue;
        $hooksQueue = $hooksQueue->withAdded($hook);
        $this->assertSame([
            $hook->for() => [
                0 => [
                    get_class($hook)
                ]
            ]
        ], $hooksQueue->toArray());
    }

    public function testWithAlreadyAddedHook(): void
    {
        $hook = new TestHook;
        $hooksQueue = (new HooksQueue)->withAdded($hook);
        $this->expectException(LogicException::class);
        $hooksQueue->withAdded($hook);
    }
}
