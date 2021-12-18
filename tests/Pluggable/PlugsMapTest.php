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

use Chevere\Components\Pluggable\PlugsMap;
use Chevere\Components\Pluggable\Types\EventPlugType;
use Chevere\Components\Pluggable\Types\HookPlugType;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Pluggable\Plug\Hook\HooksQueueInterface;
use Chevere\Tests\Pluggable\_resources\src\TestHook;
use PHPUnit\Framework\TestCase;

final class PlugsMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugType = new HookPlugType();
        $plugsMap = new PlugsMap($plugType);
        $this->assertCount(0, $plugsMap);
        $this->assertSame($plugType, $plugsMap->plugType());
        $this->expectException(OutOfBoundsException::class);
        $plugsMap->getPlugsQueueTypedFor('not-found');
    }

    public function testWithInvalidAddedPlug(): void
    {
        $plugType = new EventPlugType();
        $plugsMap = new PlugsMap($plugType);
        $hook = new TestHook();
        $this->expectException(InvalidArgumentException::class);
        $plugsMap->withAdded($hook);
    }

    public function testWithAlreadyAddedPlug(): void
    {
        $hook = new TestHook();
        $plugsMap = (new PlugsMap(new HookPlugType()))
            ->withAdded($hook);
        $this->expectException(OverflowException::class);
        $plugsMap->withAdded($hook);
    }

    public function testWithAddedPlug(): void
    {
        $hook = new TestHook();
        $hook2 = new class() extends TestHook {
            public function anchor(): string
            {
                return 'hook-anchor-2';
            }
        };
        $plugsMap = (new PlugsMap(new HookPlugType()))
            ->withAdded($hook)
            ->withAdded($hook2);
        $this->assertTrue($plugsMap->has($hook));
        $this->assertTrue($plugsMap->has($hook2));
        foreach ($plugsMap->getIterator() as $pluggableName => $plugsQueue) {
            $this->assertSame($plugsMap->getPlugsQueueTypedFor($pluggableName), $plugsQueue);
            $this->assertInstanceOf(HooksQueueInterface::class, $plugsQueue);
            $this->assertTrue($plugsMap->hasPlugsFor($pluggableName));
        }
    }
}
