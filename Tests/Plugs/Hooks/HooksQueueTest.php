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

namespace Chevere\Tests\Hooks;

use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Components\Plugs\Hooks\HooksQueue;
use Chevere\Interfaces\Plugs\Hooks\HookInterface;
use PHPUnit\Framework\TestCase;

final class HooksQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugsQueue = new PlugsQueue(new HookPlugType);
        $hooksQueue = new HooksQueue($plugsQueue);
        $this->assertSame($hooksQueue->accept(), HookInterface::class);
        $this->assertSame($plugsQueue, $hooksQueue->queue());
    }
}
