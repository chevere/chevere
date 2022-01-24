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

use Chevere\Cache\Cache;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Pluggable\Plug\Hook\HooksQueue;
use Chevere\Pluggable\PlugsMap;
use Chevere\Pluggable\PlugsMapCache;
use Chevere\Pluggable\Types\HookPlugType;
use Chevere\Tests\Pluggable\_resources\src\TestHook;
use Chevere\Tests\src\DirHelper;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\RuntimeException;
use PHPUnit\Framework\TestCase;

final class PlugsMapCacheTest extends TestCase
{
    private DirHelper $dirHelper;

    private DirInterface $emptyDir;

    private DirInterface $workingDir;

    private DirInterface $cachedDir;

    protected function setUp(): void
    {
        if (opcache_get_status() === false) {
            $this->markTestSkipped('OPCache is not enabled');
        }
        $this->dirHelper = new DirHelper($this);
        $this->emptyDir = $this->dirHelper->dir()->getChild('empty/');
        $this->workingDir = $this->dirHelper->dir()->getChild('working/');
        $this->cachedDir = $this->dirHelper->dir()->getChild('cached/');
    }

    protected function tearDown(): void
    {
        if ($this->workingDir->exists()) {
            $this->workingDir->removeContents();
        }
    }

    public function testEmpty(): void
    {
        $cache = new Cache($this->emptyDir);
        $plugsMapCache = new PlugsMapCache($cache);
        $this->assertFalse($plugsMapCache->hasPlugsQueueTypedFor('empty'));
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionCode(1);
        $plugsMapCache->getPlugsQueueTypedFor('empty');
    }

    public function testInvalidClassMap(): void
    {
        $cachedCache = new Cache($this->cachedDir);
        $cache = new Cache($cachedCache->dir()->getChild('corrupted-classmap/'));
        $plugsMapCache = new PlugsMapCache($cache);
        $this->assertFalse($plugsMapCache->hasPlugsQueueTypedFor('nothing'));
        $this->expectException(RuntimeException::class);
        $plugsMapCache->getPlugsQueueTypedFor('empty');
    }

    public function testPluggableNotMapped(): void
    {
        if (opcache_get_status() === false) {
            $this->markTestSkipped('OPCache is not enabled');
        }
        $workingCache = new Cache($this->workingDir);
        $cache = new Cache($workingCache->dir()->getChild('empty/'));
        $plugsMap = new PlugsMap(new HookPlugType());
        $plugsMapCache = new PlugsMapCache($cache);
        $plugsMapCacheWithPut = $plugsMapCache
            ->withPut($plugsMap);
        $this->assertNotSame($plugsMapCache, $plugsMapCacheWithPut);
        $this->expectException(OutOfBoundsException::class);
        $plugsMapCacheWithPut->getPlugsQueueTypedFor('workingEmpty');
    }

    public function testClassMapCorruptedQueue(): void
    {
        $cachedCache = new Cache($this->cachedDir);
        $cache = new Cache($cachedCache->dir()->getChild('corrupted-queue/'));
        $plugsMapCache = new PlugsMapCache($cache);
        $hookableClassName = (new TestHook())->at();
        $this->assertTrue($plugsMapCache->hasPlugsQueueTypedFor($hookableClassName));
        $this->expectException(RuntimeException::class);
        $plugsMapCache->getPlugsQueueTypedFor($hookableClassName);
    }

    public function testWorking(): void
    {
        $cache = new Cache($this->workingDir);
        $plugsMap = new PlugsMap(new HookPlugType());
        $hook = new TestHook();
        $hookableClassName = $hook->at();
        $plugsMap = $plugsMap->withAdded($hook);
        $plugsMapCache = (new PlugsMapCache($cache))->withPut($plugsMap);
        $this->assertTrue($plugsMapCache->hasPlugsQueueTypedFor($hookableClassName));
        $plugsQueueTyped = $plugsMapCache->getPlugsQueueTypedFor($hookableClassName);
        $this->assertInstanceOf(HooksQueue::class, $plugsQueueTyped);
        $this->assertSame([
            $hook->anchor() => [
                $hook->priority() => [
                    $hook::class,
                ],
            ],
        ], $plugsQueueTyped->plugsQueue()->toArray());
    }
}
