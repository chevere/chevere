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

namespace Chevere\Tests\Dependent;

use Chevere\Dependent\Dependencies;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Filesystem\Interfaces\PathInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use PHPUnit\Framework\TestCase;

final class DependenciesTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $dependencies = new Dependencies();
        $key = 'key';
        $this->assertCount(0, $dependencies);
        $this->assertSame(null, $dependencies->getIterator()->current());
        $this->assertSame([], $dependencies->keys());
        $this->assertFalse($dependencies->hasKey($key));
        $this->expectException(OutOfBoundsException::class);
        $dependencies->key($key);
    }

    public function testConstruct(): void
    {
        $dependencies = new Dependencies(
            path: PathInterface::class,
            dir: DirInterface::class,
        );
        $this->assertCount(2, $dependencies);
        $this->assertSame(PathInterface::class, $dependencies->getIterator()->current());
        $this->assertSame(['path', 'dir'], $dependencies->keys());
        $this->assertSame(PathInterface::class, $dependencies->key('path'));
        $this->assertSame(DirInterface::class, $dependencies->key('dir'));
    }

    public function testWithPut(): void
    {
        $dependencies = new Dependencies(path: PathInterface::class);
        $dependenciesWithPut = $dependencies->withPut(dir: DirInterface::class);
        $this->assertNotSame($dependencies, $dependenciesWithPut);
        $this->assertCount(2, $dependenciesWithPut);
        $this->assertSame(PathInterface::class, $dependenciesWithPut->getIterator()->current());
        $this->assertSame(['path', 'dir'], $dependenciesWithPut->keys());
        $this->assertSame(PathInterface::class, $dependenciesWithPut->key('path'));
        $this->assertSame(DirInterface::class, $dependenciesWithPut->key('dir'));
    }

    public function testWithMerge(): void
    {
        $dependencies = new Dependencies(path: PathInterface::class);
        $merge = new Dependencies(dir: DirInterface::class);
        $dependenciesWithMerge = $dependencies->withMerge($merge);
        $this->assertNotSame($dependencies, $dependenciesWithMerge);
        $this->assertSame(['path', 'dir'], $dependenciesWithMerge->keys());
        $this->assertSame(PathInterface::class, $dependenciesWithMerge->key('path'));
        $this->assertSame(DirInterface::class, $dependenciesWithMerge->key('dir'));
    }

    public function testWithMergeConflict(): void
    {
        $dependencies = new Dependencies(path: PathInterface::class);
        $merge = new Dependencies(path: __CLASS__);
        $this->expectException(OverflowException::class);
        $dependencies->withMerge($merge);
    }
}
