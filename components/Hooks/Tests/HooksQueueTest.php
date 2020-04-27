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

namespace Chevere\Components\X\Tests;

use Chevere\Components\Hooks\HooksQueue;
use Chevere\Components\Hooks\Tests\MyHook;
use LogicException;
use PHPUnit\Framework\TestCase;

final class HooksQueueTest extends TestCase
{
    public function testConstruct(): void
    {
        $hooksMap = new HooksQueue;
        $this->assertSame([], $hooksMap->toArray());
    }

    public function testWithHook(): void
    {
        $hook = new MyHook;
        $hooksMap = new HooksQueue;
        $hooksMap = $hooksMap->withHook($hook);
        $this->assertSame([
            $hook->anchor() => [
                0 => [
                    get_class($hook)
                ]
            ]
        ], $hooksMap->toArray());
    }

    public function testWithAlreadyAddedHook(): void
    {
        $hook = new MyHook;
        $hooksMap = (new HooksQueue)->withHook($hook);
        $this->expectException(LogicException::class);
        $hooksMap->withHook($hook);
    }
}
