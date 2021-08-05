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

namespace Chevere\Tests\Pluggable\Plug\Hook;

use Chevere\Components\Pluggable\Plug\Hook\HooksQueue;
use Chevere\Components\Pluggable\Types\HookPlugType;
use Chevere\Interfaces\Pluggable\Plug\Hook\HookInterface;
use Chevere\Tests\Pluggable\Plug\Hook\_resources\TestHook;
use PHPUnit\Framework\TestCase;

final class HooksQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $hooksQueue = new HooksQueue();
        $this->assertSame($hooksQueue->interface(), HookInterface::class);
        $this->assertInstanceOf(HookPlugType::class, $hooksQueue->getPlugType());
    }

    public function testWithAddedHook(): void
    {
        $hook = new TestHook();
        $hooksQueue = (new HooksQueue())->withAdded($hook);
        $this->assertSame([
            $hook->anchor() => [
                [
                    $hook::class,
                ],
            ],
        ], $hooksQueue->plugsQueue()->toArray());
    }
}
