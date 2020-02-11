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
use Chevere\Components\Filesystem\Exceptions\Path\PathDoesntExistsException;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase
{
    public function getPath(string $child): PathInterface
    {
        return BootstrapInstance::get()->appDir()->path()->getChild($child);
    }

    public function testFilesystemPath(): void
    {
        $this->expectNotToPerformAssertions();
        new Path('/var/fake_' . uniqid());
    }

    public function testNonExistentPath(): void
    {
        $path = new Path('/var/fake_' . uniqid());
        $this->assertFalse($path->exists());
        $this->assertFalse($path->isDir());
        $this->assertFalse($path->isFile());
    }

    public function testExistentDirPath(): void
    {
        $path = new Path(__DIR__);
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isDir());
        $this->assertFalse($path->isFile());
    }

    public function testExistentFilePath(): void
    {
        $path = new Path(__FILE__);
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        $this->assertFalse($path->isDir());
    }

    public function testExistentDirPathRemoved(): void
    {
        $path = $this->getPath('var/PathTest_dir_' . uniqid());
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

    public function testExistentFilePathRemoved(): void
    {
        $path = $this->getPath('var/PathTest_file_' . uniqid() . '.jpg');
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
        $this->expectException(PathDoesntExistsException::class);
        $path->isReadable();
    }
}
