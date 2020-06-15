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

namespace Chevere\Tests\Plugin;

use Chevere\Components\Plugin\AssertPlug;
use Chevere\Components\Plugin\PlugsMap;
use Chevere\Components\Plugin\Types\EventListenerPlugType;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Plugin\PlugRegisteredException;
use Chevere\Interfaces\Plugin\PlugsQueueInterface;
use Chevere\Tests\Plugin\_resources\src\TestHook;
use PHPUnit\Framework\TestCase;

final class PlugsMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugType = new HookPlugType;
        $plugsMap = new PlugsMap($plugType);
        $this->assertCount(0, $plugsMap);
        $this->assertSame($plugType, $plugsMap->type());
    }

    public function testWithInvalidAddedPlug(): void
    {
        $plugType = new EventListenerPlugType;
        $plugsMap = new PlugsMap($plugType);
        $hook = new TestHook;
        $this->expectException(InvalidArgumentException::class);
        $plugsMap->withAdded(new AssertPlug($hook));
    }

    public function testWithAlreadyAddedPlug(): void
    {
        $hook = new TestHook;
        $plugsMap = (new PlugsMap(new HookPlugType))
            ->withAdded(
                new AssertPlug($hook)
            );
        $this->expectException(PlugRegisteredException::class);
        $plugsMap->withAdded(
            new AssertPlug($hook)
        );
    }

    public function testWithAddedPlug(): void
    {
        $hook = new TestHook;
        $hook2 = new class extends TestHook
        {
            public function anchor(): string
            {
                return 'hook-anchor-2';
            }
        };
        $plugsMap = (new PlugsMap(new HookPlugType))
            ->withAdded(
                new AssertPlug($hook)
            )
            ->withAdded(
                new AssertPlug($hook2)
            );
        $this->assertTrue($plugsMap->has($hook));
        $this->assertTrue($plugsMap->has($hook2));
        /**
         * @var PlugsQueueInterface $plugsQueue
         */
        foreach ($plugsMap->getGenerator() as $pluggableName => $plugsQueue) {
            $this->assertSame($plugsMap->getPlugsFor($pluggableName), $plugsQueue);
            $this->assertInstanceOf(PlugsQueueInterface::class, $plugsQueue);
            $this->assertTrue($plugsMap->hasPlugsFor($pluggableName));
        }
    }
}
