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

use Chevere\Components\Cache\CacheItem;
use Chevere\Components\Filesystem\File;
use function Chevere\Components\Filesystem\fileForPath;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\VarSupport\VarStorable;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Cache\CacheItemInterface;
use Chevere\Interfaces\Filesystem\FileInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use PHPUnit\Framework\TestCase;
use function Safe\file_put_contents;

final class CacheItemTest extends TestCase
{
    private PathInterface $resourcesPath;

    protected function setUp(): void
    {
        $this->resourcesPath = (new Path(__DIR__))->getChild('_resources');
    }

    public function getDisposablePhpFileReturn(): FileInterface
    {
        $path = (new Path(__DIR__))->getChild('_resources')
            ->getChild('return-disposable.php');
        $file = fileForPath($path->__toString());
        $file->create();
        $file->put("return '';");

        return $file;
    }

    public function testVarThrowsException(): void
    {
        $file = $this->getDisposablePhpFileReturn();
        $cacheItem = $this->getCacheItem($file->path());
        $file->remove();
        $this->expectException(RuntimeException::class);
        $cacheItem->var();
    }

    public function testRawThrowsException(): void
    {
        $file = $this->getDisposablePhpFileReturn();
        $cacheItem = $this->getCacheItem($file->path());
        $file->remove();
        $this->expectException(RuntimeException::class);
        $cacheItem->raw();
    }

    public function testNotSerialized(): void
    {
        $path = $this->resourcesPath->getChild('return.php');
        $cacheItem = $this->getCacheItem($path);
        $var = include $path->__toString();
        $this->assertSame($var, $cacheItem->raw());
        $this->assertSame($var, $cacheItem->var());
    }

    public function testSerialized(): void
    {
        $path = $this->resourcesPath->getChild('return-serialized.php');
        $this->writeSerialized($path);
        $cacheItem = $this->getCacheItem($path);
        $var = include $path->__toString();
        $this->assertSame($var, $cacheItem->raw());
        $this->assertEqualsCanonicalizing(
            unserialize($var),
            $cacheItem->var()
        );
        unlink($path->__toString());
    }

    private function getCacheItem(PathInterface $path): CacheItemInterface
    {
        return new CacheItem(
            new FilePhpReturn(
                new FilePhp(
                    new File($path)
                )
            )
        );
    }

    private function writeSerialized(PathInterface $path): void
    {
        if (!$path->exists()) {
            file_put_contents($path->__toString(), '');
        }
        $fileReturn = new FilePhpReturn(
            new FilePhp(
                new File($path)
            )
        );
        $fileReturn->put(
            new VarStorable($path)
        );
    }
}
