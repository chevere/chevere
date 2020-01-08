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
use Chevere\Components\Path\PathApp;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Cache\Contracts\CacheContract;
use Chevere\Components\Cache\Contracts\CacheItemContract;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    private function getTestCache(): CacheContract
    {
        return
            new Cache(
                new Dir(
                    new PathApp('build')
                )
            );
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

    /**
     * @requires extension zend-opcache
     */
    public function testWithPutWithRemove(): void
    {
        $key = uniqid();
        $var = [time(), false, 'test', new PathApp('test'), 13.13];
        $variableExport = new VariableExport($var);
        $cacheKey = new CacheKey($key);
        $cache = $this->getTestCache()
            ->withPut($cacheKey, $variableExport);
        $this->assertArrayHasKey($key, $cache->toArray());
        $this->assertTrue($cache->exists($cacheKey));
        $this->assertInstanceOf(CacheItemContract::class, $cache->get($cacheKey));
        $cache = $cache->withRemove($cacheKey);
        $this->assertArrayNotHasKey($key, $cache->toArray());
        $this->assertFalse($cache->exists($cacheKey));
    }
}
