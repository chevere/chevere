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

namespace Chevere\Tests\Plugs\Hooks;

use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Components\Plugin\Plugs\Hooks\HooksQueue;
use Chevere\Interfaces\Plugin\Plugs\Hooks\HookInterface;
use Chevere\Tests\Plugs\Hooks\_resources\TestHook;
use PHPUnit\Framework\TestCase;

final class HooksQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $hooksQueue = new HooksQueue;
        $this->assertSame($hooksQueue->accept(), HookInterface::class);
        $this->assertEquals(new HookPlugType, $hooksQueue->getPlugType());
    }

    public function testWithAddedHook(): void
    {
        $hook = new TestHook;
        $hooksQueue = (new HooksQueue)->withAddedHook($hook);
        $this->assertSame([
            $hook->anchor() => [
                [
                    get_class($hook)
                ]
            ]
        ], $hooksQueue->queue()->toArray());
    }
}
