<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Cache;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheKeyNotFoundException;
use Chevere\Components\Dir\Dir;
use Chevere\Components\Path\Exceptions\PathIsNotDirectoryException;
use Chevere\Components\Path\Path;
use Chevere\Contracts\Cache\CacheContract;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private function getTestCache(): CacheContract
    {
        $path = new Path('build');
        $dir = new Dir($path);

        return new Cache($dir);
    }

    public function testInvalidDirContract(): void
    {
        $path = new Path('var/CacheTest_' . uniqid());
        $dir = new Dir($path);
        $this->expectException(PathIsNotDirectoryException::class);
        new Cache($dir);
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
        $this->getTestCache()
            ->get($cacheKey);
    }

    public function testWithPut(): void
    {
        $key = uniqid();
        $var = [time(), false, 'test', new Path('test'), 13.13];
        $cacheKey = new CacheKey($key);
        $cache = $this->getTestCache()
            ->withPut($cacheKey, $var);
        $this->assertArrayHasKey($key, $cache->toArray());
        $this->assertTrue($cache->exists($cacheKey));
        $fileReturn = $cache->get($cacheKey);
        $this->assertEquals($var, $fileReturn->var());
        $fileReturn->file()->remove();
    }
}
