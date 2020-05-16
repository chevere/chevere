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

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Exception\RuntimeException;
use Chevere\Components\Filesystem\FilePhpFromString;
use Chevere\Components\Filesystem\FilePhpReturnFromString;
use Chevere\Components\Plugs\AssertPlug;
use Chevere\Components\Plugs\Interfaces\PlugsQueueInterface;
use Chevere\Components\Plugs\PlugsMap;
use Chevere\Components\Plugs\PlugsRegistry;
use Chevere\Components\Plugs\Tests\_resources\src\TestHook;
use Chevere\Components\Plugs\Types\HookPlugType;
use Chevere\Components\Router\Tests\CacheHelper;
use Chevere\Components\Type\Type;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PlugsRegistryTest extends TestCase
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
        $cacheKey = new CacheKey('missing');
        $plugsRegistry = new PlugsRegistry($cache);
        $this->assertFalse($plugsRegistry->hasClassMap($cacheKey));
        $this->expectException(LogicException::class);
        $plugsRegistry->getClassMap($cacheKey);
    }

    public function testWorkingEmpty(): void
    {
        $cache = $this->cacheHelper->getWorkingCache();
        $cacheKey = new CacheKey('hooks-empty');
        $plugsMap = new PlugsMap(new HookPlugType);
        $plugsRegistry = (new PlugsRegistry($cache))
            ->withAddedClassMap($cacheKey, $plugsMap);
        $this->assertTrue($plugsRegistry->hasClassMap($cacheKey));
        $classMap = $plugsRegistry->getClassMap($cacheKey);
        $this->assertCount(0, $classMap);
    }

    public function testWorking(): void
    {
        $cache = $this->cacheHelper->getWorkingCache();
        $cacheKey = new CacheKey('hooks-some');
        $plugsMap = new PlugsMap(new HookPlugType);
        $hook = new TestHook;
        $plugsMap = $plugsMap->withAddedPlug(
            new AssertPlug($hook)
        );
        $plugsRegistry = (new PlugsRegistry($cache))
            ->withAddedClassMap($cacheKey, $plugsMap);
        $this->assertTrue($plugsRegistry->hasClassMap($cacheKey));
        $classMap = $plugsRegistry->getClassMap($cacheKey);
        $this->assertCount(1, $classMap);
        $this->assertTrue($classMap->has($hook->at()));
        $hooks = $classMap->get($hook->at());
        $phpFileReturn = new FilePhpReturnFromString($hooks);
        /**
         * @var PlugsQueueInterface $plugsQueue
         */
        $plugsQueue = $phpFileReturn->varType(new Type(PlugsQueueInterface::class));
        $this->assertSame([
            $hook->anchor() => [
                $hook->priority() => [
                    get_class($hook)
                ]
            ]
        ], $plugsQueue->toArray());
    }

    public function testCachedInvalid(): void
    {
        $cache = $this->cacheHelper->getCachedCache();
        $cacheKey = new CacheKey('hooks-invalid');
        $plugsRegistry = new PlugsRegistry($cache);
        $this->assertTrue($plugsRegistry->hasClassMap($cacheKey));
        $this->expectException(RuntimeException::class);
        $plugsRegistry->getClassMap($cacheKey);
    }

    public function testCachedCorrupted(): void
    {
        $cache = $this->cacheHelper->getCachedCache();
        $cacheKey = new CacheKey('hooks-corrupted');
        $plugsRegistry = new PlugsRegistry($cache);
        $this->assertTrue($plugsRegistry->hasClassMap($cacheKey));
        $this->expectException(RuntimeException::class);
        $plugsRegistry->getClassMap($cacheKey);
    }
}
