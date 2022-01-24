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

use Chevere\Filesystem\Dir;
use Chevere\Filesystem\Exceptions\PathNotExistsException;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Filesystem\Interfaces\PathInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

final class PathTest extends TestCase
{
    private PathInterface $testPath;

    private DirInterface $testDir;

    protected function setUp(): void
    {
        $this->testPath = new Path(__DIR__ . '/PathTest_' . uniqid() . '/');
        $this->testDir = new Dir(new Path($this->testPath->__toString()));
    }

    protected function tearDown(): void
    {
        try {
            $this->testDir->removeContents();
        } catch (Throwable $e) {
            //$e
        }

        try {
            $this->testDir->remove();
        } catch (Throwable $e) {
            //$e
        }
    }

    public function testIsReadable(): void
    {
        $this->testDir->create();
        $this->assertTrue($this->testPath->isWritable());
        $this->testDir->remove();
        $this->expectException(PathNotExistsException::class);
        $this->testPath->isReadable();
    }

    public function testIsWritable(): void
    {
        $this->testDir->create();
        $this->assertTrue($this->testPath->isWritable());
        $this->testDir->remove();
        $this->expectException(PathNotExistsException::class);
        $this->testPath->isWritable();
    }

    public function testNonExistentPath(): void
    {
        $this->assertFalse($this->testPath->exists());
        $this->assertFalse($this->testPath->isDir());
        $this->assertFalse($this->testPath->isFile());
        $this->expectException(PathNotExistsException::class);
        $this->testPath->assertExists();
    }

    public function testExistentDirPath(): void
    {
        $path = new Path(__DIR__);
        $path->assertExists();
        $this->assertTrue($path->exists());
        $this->assertTrue($path->isDir());
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
        $this->assertFalse($path->isDir());
    }

    public function testExistentDirPathRemoved(): void
    {
        $this->assertFalse($this->testPath->exists());
        if (!mkdir($this->testPath->__toString(), 0777, true)) {
            throw new RuntimeException('Unable to create dir ' . $this->testPath->__toString());
        }
        $this->assertTrue($this->testPath->exists());
        $this->assertTrue($this->testPath->isDir());
        if (!rmdir($this->testPath->__toString())) {
            throw new RuntimeException('Unable to remove dir ' . $this->testPath->__toString());
        }
        $this->assertFalse($this->testPath->exists());
        $this->assertFalse($this->testPath->isDir());
    }

    public function testExistentFilePathRemoved(): void
    {
        $this->testDir->create();
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
