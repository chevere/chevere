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

namespace Chevere\Tests\Path;

use RuntimeException;

use Chevere\Components\Path\Exceptions\PathInvalidException;
use Chevere\Components\Path\Exceptions\PathNotAllowedException;
use Chevere\Components\Path\Path;
use Chevere\Contracts\Path\PathContract;
use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase
{
    public function testWithInvalidSuperiorPath(): void
    {
        $root = PathContract::ROOT;
        $uber = dirname($root);
        if ($uber == $root) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(PathNotAllowedException::class);
        }
        new Path($uber);
    }

    public function testWithExtraSlashesPath(): void
    {
        $this->expectException(PathInvalidException::class);
        new Path('some//dir');
    }

    public function testWithDotsPath(): void
    {
        $this->expectException(PathInvalidException::class);
        new Path('some/../dir');
    }
    
    public function testWithStrictRelativePath(): void
    {
        $this->expectException(PathInvalidException::class);
        new Path('./dir');
    }

    public function testWithRelativePath(): void
    {
        $absolute = PathContract::ROOT . 'dir';
        $path = new Path('dir');
        $this->assertSame('dir', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithRelativePathTrailing(): void
    {
        $absolute = PathContract::ROOT . 'dir/';
        $path = new Path('dir/');
        $this->assertSame('dir/', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithAbsolutePath(): void
    {
        $absolute = PathContract::ROOT . 'dir/';
        $path = new Path($absolute);
        $this->assertSame('dir/', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithAbsolutePathTrailing(): void
    {
        $absolute = PathContract::ROOT . 'dir/';
        $path = new Path($absolute);
        $this->assertSame('dir/', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithNonExistentPath(): void
    {
        $path = new Path('fake_' . uniqid());
        $this->assertFalse($path->exists());
        $this->assertFalse($path->isDir());
        $this->assertFalse($path->isFile());
    }

    public function testWithExistentDirPath(): void
    {
        $path = new Path('src');
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isDir());
        $this->assertFalse($path->isFile());
    }
    
    public function testWithExistentFilePath(): void
    {
        $path = new Path('parameters.php');
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        $this->assertFalse($path->isDir());
    }

    public function testWithExistentDirPathRemoved(): void
    {
        $path = new Path('var/PathTest_path_' . uniqid());
        $this->assertFalse($path->exists());
        if (!mkdir($path->absolute(), 0777, true)) {
            throw new RuntimeException('Unable to create dir ' . $path->absolute());
        }
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isDir());
        if (!rmdir($path->absolute())) {
            throw new RuntimeException('Unable to remove dir ' . $path->absolute());
        }
        $this->assertFalse($path->exists());
        $this->assertFalse($path->isDir());
    }

    public function testWithExistentFilePathRemoved(): void
    {
        $path = new Path('var/PathTest_file_' . uniqid() . '.jpg');
        $this->assertFalse($path->exists());
        if (false === file_put_contents($path->absolute(), 'una mona pilucha')) {
            throw new RuntimeException('Unable to create file ' . $path->absolute());
        }
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        if (!unlink($path->absolute())) {
            throw new RuntimeException('Unable to remove file ' . $path->absolute());
        }
        $this->assertFalse($path->exists());
        $this->assertFalse($path->isFile());
    }
}
