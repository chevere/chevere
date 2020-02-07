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

namespace Chevere\Components\Cache\Tests;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Cache\Interfaces\CacheItemInterface;
use Chevere\Components\Filesystem\Path;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private function getTestCache(): CacheInterface
    {
        return new Cache(new Dir(new AppPath('build')));
    }

    public function testConstructor(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getTestCache();
    }

    public function testKeyNotExists(): void
    {
        $cache = $this->getTestCache();
        $cacheKey = new CacheKey(uniqid());
        $this->assertFalse($cache->exists($cacheKey));
    }

    public function testGetNotExists(): void
    {
        $cacheKey = new CacheKey(uniqid());
        $this->expectException(CacheKeyNotFoundException::class);
        $this->getTestCache()->get($cacheKey);
    }

    public function testWithPutWithRemove(): void
    {
        $key = uniqid();
        $var = [time(), false, 'test', new AppPath('test'), 13.13];
        $variableExport = new VariableExport($var);
        $cacheKey = new CacheKey($key);
        $cache = $this->getTestCache()
            ->withPut($cacheKey, $variableExport);
        $this->assertArrayHasKey($key, $cache->puts());
        $this->assertTrue($cache->exists($cacheKey));
        $this->assertInstanceOf(CacheItemInterface::class, $cache->get($cacheKey));
        $cache = $cache->withRemove($cacheKey);
        $this->assertArrayNotHasKey($key, $cache->puts());
        $this->assertFalse($cache->exists($cacheKey));
    }
}
