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

namespace Chevere\Components\Filesystem\Path\Tests;

use RuntimeException;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Exceptions\Path\PathDotSlashException;
use Chevere\Components\Filesystem\Exceptions\Path\PathInvalidException;
use Chevere\Components\Filesystem\Exceptions\Path\PathOmitRelativeException;
use Chevere\Components\Filesystem\Exceptions\Path\PathNotAllowedException;
use Chevere\Components\Filesystem\AppPath;
use PHPUnit\Framework\TestCase;

final class AppPathTest extends TestCase
{
    private function getAppDir(): DirInterface
    {
        return BootstrapInstance::get()->appDir();
    }

    public function testWithInvalidSuperiorPath(): void
    {
        $root = $this->getAppDir();
        $uber = dirname($root->path()->absolute());
        if ($uber == $root) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->expectException(PathNotAllowedException::class);
        }
        new AppPath($uber);
    }

    public function testDotSlashPath(): void
    {
        $this->expectException(PathDotSlashException::class);
        new AppPath('./dir');
    }

    public function testWithRelativePath(): void
    {
        $child = $this->getAppDir()->getChild('dir');
        $path = new AppPath('dir');
        $this->assertSame('dir', $path->relative());
        $this->assertSame($child->path()->absolute(), $path->absolute() . '/');
    }

    public function testWithAbsolutePath(): void
    {
        $absolute = $this->getAppDir()->path()->absolute() . 'dir';
        $path = new AppPath($absolute);
        $this->assertSame('dir', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithRelativePathTrailing(): void
    {
        $absolute = $this->getAppDir()->getChild('dir')->path()->absolute();
        $path = new AppPath('dir/');
        $this->assertSame('dir/', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    public function testWithAbsolutePathTrailing(): void
    {
        $absolute = $this->getAppDir()->getChild('dir')->path()->absolute();
        $path = new AppPath($absolute);
        $this->assertSame('dir/', $path->relative());
        $this->assertSame($absolute, $path->absolute());
    }

    // public function testWithNonExistentPath(): void
    // {
    //     $path = new AppPath('var/fake_' . uniqid());
    //     $this->assertFalse($path->exists());
    //     $this->assertFalse($path->isDir());
    //     $this->assertFalse($path->isFile());
    // }

    // public function testWithExistentDirPath(): void
    // {
    //     $path = new AppPath('var');
    //     $this->assertTrue($path->exists());
    //     $this->assertTrue($path->isDir());
    //     $this->assertFalse($path->isFile());
    // }

    // public function testWithExistentFilePath(): void
    // {
    //     $path = new AppPath('parameters.php');
    //     $this->assertTrue($path->exists());
    //     $this->assertTrue($path->isFile());
    //     $this->assertFalse($path->isDir());
    // }

    // public function testWithExistentDirPathRemoved(): void
    // {
    //     $path = new AppPath('var/PathTest_dir_' . uniqid());
    //     $this->assertFalse($path->exists());
    //     if (!mkdir($path->absolute(), 0777, true)) {
    //         throw new RuntimeException('Unable to create dir ' . $path->absolute());
    //     }
    //     $this->assertTrue($path->exists());
    //     $this->assertTrue($path->isDir());
    //     if (!rmdir($path->absolute())) {
    //         throw new RuntimeException('Unable to remove dir ' . $path->absolute());
    //     }
    //     $this->assertFalse($path->exists());
    //     $this->assertFalse($path->isDir());
    // }

    // public function testWithExistentFilePathRemoved(): void
    // {
    //     $path = new AppPath('var/PathTest_file_' . uniqid() . '.jpg');
    //     $this->assertFalse($path->exists());
    //     if (false === file_put_contents($path->absolute(), 'una mona pilucha')) {
    //         throw new RuntimeException('Unable to create file ' . $path->absolute());
    //     }
    //     $this->assertTrue($path->exists());
    //     $this->assertTrue($path->isFile());
    //     if (!unlink($path->absolute())) {
    //         throw new RuntimeException('Unable to remove file ' . $path->absolute());
    //     }
    //     $this->assertFalse($path->exists());
    //     $this->assertFalse($path->isFile());
    // }
}
