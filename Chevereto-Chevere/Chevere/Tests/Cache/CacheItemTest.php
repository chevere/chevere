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

use Chevere\Components\Cache\CacheItem;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\File\FileReturn;
use Chevere\Components\Path\Path;
use PHPUnit\Framework\TestCase;

final class CacheItemTest extends TestCase
{
    public function testConstruct(): void
    {
        $path = new Path(__DIR__ . '/resources/return.php');
        $cacheItem =
            new CacheItem(
                new FileReturn(
                    new FilePhp(
                        new File($path)
                    )
                )
            );
        $this->assertSame(include $path->absolute(), $cacheItem->raw());
    }
}
