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

use Chevere\Components\Cache\Cache;
use Chevere\Components\Plugin\AssertPlug;
use Chevere\Components\Plugin\Plugs\Hooks\HooksQueue;
use Chevere\Components\Plugin\PlugsMap;
use Chevere\Components\Plugin\PlugsMapCache;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Tests\Plugin\_resources\src\TestHook;
use Chevere\Tests\Router\CacheHelper;
use PHPUnit\Framework\TestCase;
use Throwable;

final class PlugsMapCacheTest extends TestCase
{
    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__, $this);
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testEmpty(): void
    {
        $cache = $this->cacheHelper->getEmptyCache();
        $plugsMapCache = new PlugsMapCache($cache);
        $this->assertFalse($plugsMapCache->hasPlugsQueueFor('empty'));
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionCode(1);
        $plugsMapCache->getPlugsQueueFor('empty');
    }

    public function testInvalidClassMap(): void
    {
        $cachedCache = $this->cacheHelper->getCachedCache();
        $cache = new Cache($cachedCache->dir()->getChild('corrupted-classmap/'));
        $plugsMapCache = new PlugsMapCache($cache);
        $this->assertFalse($plugsMapCache->hasPlugsQueueFor('nothing'));
        $this->expectException(RuntimeException::class);
        $plugsMapCache->getPlugsQueueFor('empty');
    }

    public function testPluggableNotMapped(): void
    {
        $workingCache = $this->cacheHelper->getWorkingCache();
        $cache = new Cache($workingCache->dir()->getChild('empty/'));
        $plugsMap = new PlugsMap(new HookPlugType);
        $plugsMapCache = (new PlugsMapCache($cache))
            ->withPut($plugsMap);
        $this->expectException(OutOfBoundsException::class);
        $plugsMapCache->getPlugsQueueFor('workingEmpty');
    }

    public function testClassMapCorruptedQueue(): void
    {
        $cachedCache = $this->cacheHelper->getCachedCache();
        $cache = new Cache($cachedCache->dir()->getChild('corrupted-queue/'));
        $plugsMapCache = new PlugsMapCache($cache);
        $hookableClassName = (new TestHook)->at();
        $this->assertTrue($plugsMapCache->hasPlugsQueueFor($hookableClassName));
        $this->expectException(OutOfBoundsException::class);
        $plugsMapCache->getPlugsQueueFor($hookableClassName);
    }

    public function testWorking(): void
    {
        $cache = $this->cacheHelper->getWorkingCache();
        $plugsMap = new PlugsMap(new HookPlugType);
        $hook = new TestHook;
        $hookableClassName = $hook->at();
        $plugsMap = $plugsMap->withAdded(
            new AssertPlug($hook)
        );
        $plugsMapCache = (new PlugsMapCache($cache))->withPut($plugsMap);
        $this->assertTrue($plugsMapCache->hasPlugsQueueFor($hookableClassName));
        $plugsQueueTyped = $plugsMapCache->getPlugsQueueFor($hookableClassName);
        $this->assertInstanceOf(HooksQueue::class, $plugsQueueTyped);
        $this->assertSame([
            $hook->anchor() => [
                $hook->priority() => [
                    get_class($hook)
                ]
            ]
        ], $plugsQueueTyped->plugsQueue()->toArray());
    }
}
