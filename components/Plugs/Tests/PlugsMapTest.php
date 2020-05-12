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

use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Plugs\AssertPlug;
use Chevere\Components\Plugs\Exceptions\PlugRegisteredException;
use Chevere\Components\Plugs\Interfaces\PlugsQueueInterface;
use Chevere\Components\Plugs\PlugsMap;
use Chevere\Components\Plugs\Tests\_resources\src\TestHook;
use PHPUnit\Framework\TestCase;

final class PlugsMapTest extends TestCase
{
    public function testConstrut(): void
    {
        $plugsMap = new PlugsMap;
        $this->assertCount(0, $plugsMap);
    }

    public function testWithAddedPlug(): void
    {
        $hook = new TestHook;
        $hook2 = new class extends TestHook
        {
            public function for(): string
            {
                return 'hook-anchor-2';
            }
        };
        $plugsMap = (new PlugsMap)
            ->withAddedPlug(
                new AssertPlug($hook)
            )
            ->withAddedPlug(
                new AssertPlug($hook2)
            );
        /**
         * @var PlugsQueueInterface $plugsQueue
         */
        foreach ($plugsMap->getGenerator() as $plugableName => $plugsQueue) {
            $this->assertInstanceOf(PlugsQueueInterface::class, $plugsQueue);
            $this->assertTrue($plugsMap->hasPlugable($plugableName));
        }
    }

    public function testWithAlreadyAddedPlug(): void
    {
        $hook = new TestHook;
        $plugsMap = (new PlugsMap)
            ->withAddedPlug(
                new AssertPlug($hook)
            );
        $this->expectException(PlugRegisteredException::class);
        $plugsMap->withAddedPlug(
            new AssertPlug($hook)
        );
    }
}
