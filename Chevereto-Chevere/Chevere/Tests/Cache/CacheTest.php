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
use Chevere\Components\Dir\Dir;
use Chevere\Components\Path\Path;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    public function testInvalidDirContract(): void
    {
        $path = new Path('test');
        $dir = new Dir($path);
        $cacheKey = new CacheKey('keyTest');
        $cache = new Cache($cacheKey, $dir);
        // dd($cache);
    }

    // public function testConstructor(): void
    // {
    //     $path = new Path('test');
    //     $dir = new Dir($path);
    //     $cacheKey = new CacheKey('keyTest');
    //     $cache = new Cache($cacheKey, $dir);
    //     dd($cache);
    // }
}
