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

namespace Chevere\Components\Path\Tests;

use RuntimeException;
use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Path\Exceptions\PathInvalidException;
use Chevere\Components\Path\Exceptions\PathOmitRelativeException;
use Chevere\Components\Path\Exceptions\PathNotAllowedException;
use Chevere\Components\Path\PathApp;
use PHPUnit\Framework\TestCase;

final class PathAppTest extends TestCase
{
    private function getAppPath(): string
    {
        return BootstrapInstance::get()->appPath();
    }

    public function testWithInvalidSuperiorPath(): void
    {
        $root = $this->getAppPath();
        $uber = dirname($root);
        if ($uber == $root) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(PathNotAllowedException::class);
        }
        new PathApp($uber);
    }

    public function testWithStrictRelativePath(): void
    {
        $this->expectException(PathOmitRelativeException::class);
        new PathApp('./dir');
    }

    public function testWithRelativePath(): void
    {
        $absolute = $this->getAppPath() . 'dir';
        $path = new PathApp('dir');
        $this->assertSame('dir', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithRelativePathTrailing(): void
    {
        $absolute = $this->getAppPath() . 'dir/';
        $path = new PathApp('dir/');
        $this->assertSame('dir/', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithAbsolutePath(): void
    {
        $absolute = $this->getAppPath() . 'dir';
        $path = new PathApp($absolute);
        $this->assertSame('dir', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithAbsolutePathTrailing(): void
    {
        $absolute = $this->getAppPath() . 'dir/';
        $path = new PathApp($absolute);
        $this->assertSame('dir/', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithNonExistentPath(): void
    {
        $path = new PathApp('var/fake_' . uniqid());
        $this->assertFalse($path->exists());
        $this->assertFalse($path->isDir());
        $this->assertFalse($path->isFile());
    }

    public function testWithExistentDirPath(): void
    {
        $path = new PathApp('var');
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isDir());
        $this->assertFalse($path->isFile());
    }

    public function testWithExistentFilePath(): void
    {
        $path = new PathApp('parameters.php');
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        $this->assertFalse($path->isDir());
    }

    public function testWithExistentDirPathRemoved(): void
    {
        $path = new PathApp('var/PathTest_dir_' . uniqid());
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
        $path = new PathApp('var/PathTest_file_' . uniqid() . '.jpg');
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
