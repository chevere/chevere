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
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\VarExportable\VarExportable;
use Chevere\Exceptions\Cache\CacheKeyNotFoundException;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private DirInterface $dir;

    protected function setUp(): void
    {
        $this->dir = new Dir(
            new Path(__DIR__ . '/_resources/CacheTest/cache/')
        );
    }

    public function testConstructor(): void
    {
        $cache = new Cache($this->dir);
        $this->assertSame($cache->dir(), $this->dir);
    }

    public function testKeyNotExists(): void
    {
        $cache = new Cache($this->dir);
        $cacheKey = new CacheKey(uniqid());
        $this->assertFalse($cache->exists($cacheKey));
    }

    public function testGetNotExists(): void
    {
        $cacheKey = new CacheKey(uniqid());
        $this->expectException(CacheKeyNotFoundException::class);
        (new Cache($this->dir))->get($cacheKey);
    }

    public function testWithPutWithRemove(): void
    {
        $key = uniqid();
        $var = [time(), false, 'test', $this->dir->getChild('test/'), 13.13];
        $varExportable = new VarExportable($var);
        $cacheKey = new CacheKey($key);
        $cache = (new Cache($this->dir))
            ->withPut($cacheKey, $varExportable);
        $this->assertArrayHasKey($key, $cache->puts());
        $this->assertTrue($cache->exists($cacheKey));
        $this->assertInstanceOf(CacheItemInterface::class, $cache->get($cacheKey));
        $cache = $cache->without($cacheKey);
        $this->assertArrayNotHasKey($key, $cache->puts());
        $this->assertFalse($cache->exists($cacheKey));
    }
}
