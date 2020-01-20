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

namespace Chevere\Tests\CacheKeyTest;

use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Exceptions\CacheInvalidKeyException;
use PHPUnit\Framework\TestCase;

final class CacheKeyTest extends TestCase
{
    public function testInvalidArgumentConstruct()
    {
        $this->expectException(CacheInvalidKeyException::class);
        new CacheKey('./\\~:');
    }

    public function testConstruct(): void
    {
        $key = 'test';
        $cacheKey = new CacheKey($key);
        $this->assertSame($key, $cacheKey->toString());
    }
}
