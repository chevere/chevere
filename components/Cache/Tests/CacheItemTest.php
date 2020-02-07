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

use Chevere\Components\Cache\CacheItem;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\FileReturn;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Cache\Interfaces\CacheItemInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;

final class CacheItemTest extends TestCase
{
    public function setUp(): void
    {
        $this->resourcesPath = (new Path(__DIR__))->getChild('_resources');
    }

    private function getCacheItem(PathInterface $path): CacheItemInterface
    {
        return
            new CacheItem(
                new FileReturn(
                    new PhpFile(
                        new File($path)
                    )
                )
            );
    }

    private function writeSerialized(PathInterface $path): void
    {
        $fileReturn =
            new FileReturn(
                new PhpFile(
                    new File($path)
                )
            );
        $fileReturn->put(
            new VariableExport($path)
        );
    }

    public function testNotSerialized(): void
    {
        $path = $this->resourcesPath->getChild('return.php');
        $cacheItem = $this->getCacheItem($path);
        $var = include $path->absolute();
        $this->assertSame($var, $cacheItem->raw());
        $this->assertSame($var, $cacheItem->var());
    }

    public function testSerialized(): void
    {
        // TODO: Pass a path for storing on-the-fly stuff
        $path = $this->resourcesPath->getChild('return-serialized.php');
        $this->writeSerialized($path);
        $cacheItem = $this->getCacheItem($path);
        $var = include $path->absolute();
        // xdd($cacheItem);
        $this->assertSame($var, $cacheItem->raw());
        $this->assertEquals(unserialize($var), $cacheItem->var());
    }
}
