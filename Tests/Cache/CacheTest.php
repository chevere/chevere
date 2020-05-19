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

namespace Chevere\Tests\Cache;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Interfaces\Cache\CacheInterface;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Components\Filesystem\Dir;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\VarExportable\VarExportable;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private PathInterface $path;

    public function setUp(): void
    {
        $this->path = new Path(__DIR__ . '/_resources/CacheTest/');
    }

    private function getTestCache(): CacheInterface
    {
        return new Cache(new Dir($this->path->getChild('build/')));
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
        $var = [time(), false, 'test', $this->path->getChild('test'), 13.13];
        $varExportable = new VarExportable($var);
        $cacheKey = new CacheKey($key);
        $cache = $this->getTestCache()
            ->withPut($cacheKey, $varExportable);
        $this->assertArrayHasKey($key, $cache->puts());
        $this->assertTrue($cache->exists($cacheKey));
        $this->assertInstanceOf(CacheItemInterface::class, $cache->get($cacheKey));
        $cache = $cache->withRemove($cacheKey);
        $this->assertArrayNotHasKey($key, $cache->puts());
        $this->assertFalse($cache->exists($cacheKey));
    }
}
