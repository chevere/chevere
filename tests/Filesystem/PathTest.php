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

namespace Chevere\Tests\Filesystem;

use Chevere\Filesystem\Directory;
use Chevere\Filesystem\Exceptions\PathNotExistsException;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Interfaces\PathInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

final class PathTest extends TestCase
{
    private PathInterface $testPath;

    private DirectoryInterface $testDirectory;

    protected function setUp(): void
    {
        $this->testPath = new Path(__DIR__ . '/PathTest_' . uniqid() . '/');
        $this->testDirectory = new Directory(new Path($this->testPath->__toString()));
    }

    protected function tearDown(): void
    {
        try {
            $this->testDirectory->removeContents();
        } catch (Throwable $e) {
            //$e
        }

        try {
            $this->testDirectory->remove();
        } catch (Throwable $e) {
            //$e
        }
    }

    public function testIsReadable(): void
    {
        $this->testDirectory->create();
        $this->assertTrue($this->testPath->isWritable());
        $this->testDirectory->remove();
        $this->expectException(PathNotExistsException::class);
        $this->testPath->isReadable();
    }

    public function testIsWritable(): void
    {
        $this->testDirectory->create();
        $this->assertTrue($this->testPath->isWritable());
        $this->testDirectory->remove();
        $this->expectException(PathNotExistsException::class);
        $this->testPath->isWritable();
    }

    public function testNonExistentPath(): void
    {
        $this->assertFalse($this->testPath->exists());
        $this->assertFalse($this->testPath->isDirectory());
        $this->assertFalse($this->testPath->isFile());
        $this->expectException(PathNotExistsException::class);
        $this->testPath->assertExists();
    }

    public function testExistentDirectoryPath(): void
    {
        $path = new Path(__DIR__);
        $path->assertExists();
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isDirectory());
        $this->assertFalse($path->isFile());
        $this->assertTrue($path->isReadable());
        $this->assertTrue($path->isWritable());
    }

    public function testExistentFilePath(): void
    {
        $path = new Path(__FILE__);
        $path->assertExists();
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        $this->assertFalse($path->isDirectory());
    }

    public function testExistentDirectoryPathRemoved(): void
    {
        $this->assertFalse($this->testPath->exists());
        if (!mkdir($this->testPath->__toString(), 0777, true)) {
            throw new RuntimeException('Unable to create directory ' . $this->testPath->__toString());
        }
        $this->assertTrue($this->testPath->exists());
        $this->assertTrue($this->testPath->isDirectory());
        if (!rmdir($this->testPath->__toString())) {
            throw new RuntimeException('Unable to remove directory ' . $this->testPath->__toString());
        }
        $this->assertFalse($this->testPath->exists());
        $this->assertFalse($this->testPath->isDirectory());
    }

    public function testExistentFilePathRemoved(): void
    {
        $this->testDirectory->create();
        $path = $this->testPath->getChild('test.txt');
        $this->assertFalse($path->exists());
        if (file_put_contents($path->__toString(), 'file put contents') === false) {
            throw new RuntimeException('Unable to create file ' . $path->__toString());
        }
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isFile());
        if (!unlink($path->__toString())) {
            throw new RuntimeException('Unable to remove file ' . $path->__toString());
        }
        $this->assertFalse($path->exists());
        $this->assertFalse($path->isFile());
        $this->expectException(PathNotExistsException::class);
        $path->isReadable();
    }
}
