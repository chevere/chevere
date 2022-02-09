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

use Chevere\Cache\Cache;
use Chevere\Cache\CacheKey;
use Chevere\Cache\Interfaces\CacheItemInterface;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Tests\src\DirHelper;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\VarSupport\VarStorable;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private DirInterface $resourcesDir;

    protected function setUp(): void
    {
        $this->resourcesDir = (new DirHelper($this))->dir();
        $this->resourcesDir->createIfNotExists();
    }

    public function tearDown(): void
    {
        $this->resourcesDir->removeIfExists();
    }

    public function testConstruct(): void
    {
        $cache = new Cache($this->resourcesDir);
        $this->assertSame($cache->dir(), $this->resourcesDir);
    }

    public function testConstructDirNotExists(): void
    {
        $dir = $this->resourcesDir->getChild('delete/');
        $dirPath = $dir->path()->__toString();
        $this->assertDirectoryDoesNotExist($dir->path()->__toString());
        new Cache($dir);
        $this->assertDirectoryExists($dirPath);
        $dir->remove();
    }

    public function testKeyNotExists(): void
    {
        $cache = new Cache($this->resourcesDir);
        $cacheKey = new CacheKey(uniqid());
        $this->assertFalse($cache->exists($cacheKey));
    }

    public function testGetNotExists(): void
    {
        $cacheKey = new CacheKey(uniqid());
        $this->expectException(OutOfBoundsException::class);
        (new Cache($this->resourcesDir))->get($cacheKey);
    }

    public function testWithPutWithRemove(): void
    {
        $key = uniqid();
        $var = [
            time(),
            false,
            'test',
            $this->resourcesDir->getChild('test/'),
            13.13
        ];
        $varStorable = new VarStorable($var);
        $cacheKey = new CacheKey($key);
        $cache = new Cache($this->resourcesDir);
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
