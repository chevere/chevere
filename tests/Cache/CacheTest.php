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
use Chevere\Components\VarSupport\VarStorable;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private DirInterface $dir;

    protected function setUp(): void
    {
        if (opcache_get_status() === false) {
            $this->markTestSkipped('OPCache is not enabled');
        }
        $this->dir = new Dir(
            new Path(__DIR__ . '/_resources/CacheTest/cache/')
        );
    }

    public function testConstruct(): void
    {
        $cache = new Cache($this->dir);
        $this->assertSame($cache->dir(), $this->dir);
    }

    public function testConstructDirNotExists(): void
    {
        $dir = $this->dir->getChild('delete/');
        $dirPath = $dir->path()->__toString();
        $this->assertDirectoryDoesNotExist($dir->path()->__toString());
        new Cache($dir);
        $this->assertDirectoryExists($dirPath);
        $dir->remove();
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
        $this->expectException(OutOfBoundsException::class);
        (new Cache($this->dir))->get($cacheKey);
    }

    public function testWithPutWithRemove(): void
    {
        $key = uniqid();
        $var = [
            time(),
            false,
            'test',
            $this->dir->getChild('test/'),
            13.13
        ];
        $varStorable = new VarStorable($var);
        $cacheKey = new CacheKey($key);
        $cache = new Cache($this->dir);
        $cacheWithPut = $cache->withPut($cacheKey, $varStorable);
        $this->assertNotSame($cache, $cacheWithPut);
        $this->assertArrayHasKey($key, $cacheWithPut->puts());
        $this->assertArrayHasKey(
            'path',
            $cacheWithPut->puts()[$key]
        );
        $this->assertArrayHasKey(
            'checksum',
            $cacheWithPut->puts()[$key]
        );
        $this->assertTrue($cacheWithPut->exists($cacheKey));
        $this->assertInstanceOf(
            CacheItemInterface::class,
            $cacheWithPut->get($cacheKey)
        );
        $cacheWithout = $cacheWithPut->without($cacheKey);
        $this->assertNotSame($cacheWithPut, $cacheWithout);
        $this->assertArrayNotHasKey($key, $cacheWithout->puts());
        $this->assertFalse($cacheWithout->exists($cacheKey));
    }
}
