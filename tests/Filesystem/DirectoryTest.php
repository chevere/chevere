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
use Chevere\Filesystem\Exceptions\DirectoryExistsException;
use Chevere\Filesystem\Exceptions\DirectoryNotExistsException;
use Chevere\Filesystem\Exceptions\PathIsFileException;
use Chevere\Filesystem\Exceptions\PathIsNotDirectoryException;
use Chevere\Filesystem\Exceptions\PathTailException;
use Chevere\Filesystem\File;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use Throwable;

final class DirectoryTest extends TestCase
{
    private DirectoryInterface $testDirectory;

    protected function setUp(): void
    {
        $backTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['line'];

        $this->testDirectory = new Directory(
            new Path(__DIR__ . '/DirTest_' . uniqid() . '_' . $backTrace . '/')
        );
    }

    protected function tearDown(): void
    {
        try {
            $this->testDirectory->remove();
        } catch (Throwable) {
            // Ignore fs issues
        }
    }

    public function testWithFilePath(): void
    {
        $path = new Path(__DIR__ . '/no-tail');
        $this->expectException(PathTailException::class);
        new Directory($path);
    }

    public function testWithActualFilePath(): void
    {
        $path = new Path(__FILE__);
        $this->expectException(PathIsFileException::class);
        new Directory($path);
    }

    public function testNotExists(): void
    {
        $this->assertFalse($this->testDirectory->getChild('not-exists/')->exists());
        $this->assertSame(
            [],
            $this->testDirectory->getChild('not-exists/')->removeIfExists()
        );
    }

    public function testAssertExists(): void
    {
        $this->expectException(DirectoryNotExistsException::class);
        $this->testDirectory->getChild('not-exists/')->assertExists();
    }

    public function testCreate(): void
    {
        $this->testDirectory->create();
        $this->testDirectory->assertExists();
        $this->assertTrue($this->testDirectory->exists());
        $this->testDirectory->removeIfExists();
        $this->assertFalse($this->testDirectory->exists());
    }

    public function testCreateIfNotExists(): void
    {
        $this->testDirectory->createIfNotExists();
        $this->testDirectory->createIfNotExists();
        $this->assertTrue($this->testDirectory->exists());
    }

    public function testCreateDirExists(): void
    {
        $this->expectException(DirectoryExistsException::class);
        (new Directory(new Path(__DIR__ . '/')))->create();
    }

    public function testRemoveNonExistentPath(): void
    {
        $this->expectException(PathIsNotDirectoryException::class);
        $this->testDirectory->remove();
    }

    public function testRemove(): void
    {
        $this->testDirectory->create();
        $this->testDirectory->getChild('child/')->create();
        $removed = $this->testDirectory->remove();
        $this->assertContainsEquals($this->testDirectory->path()->__toString(), $removed);
        $this->assertFalse($this->testDirectory->exists());
        $this->expectException(PathIsNotDirectoryException::class);
        $this->testDirectory->remove();
    }

    public function testRemoveContents(): void
    {
        $this->testDirectory->create();
        $childDir1 = new Directory($this->testDirectory->path()->getChild('dir1/'));
        $childFile1 = new File($this->testDirectory->path()->getChild('file1'));
        $childFile2 = new File($this->testDirectory->path()->getChild('file2'));
        $childDir1->create();
        $childFile1->create();
        $childFile2->create();
        $removed = $this->testDirectory->removeContents();
        $this->assertCount(3, $removed);
        $this->assertNotContainsEquals($this->testDirectory->path()->__toString(), $removed);
        $this->assertContainsEquals($childFile1->path()->__toString(), $removed);
        $this->assertContainsEquals($childFile1->path()->__toString(), $removed);
        $this->assertContainsEquals($childFile2->path()->__toString(), $removed);
        $this->testDirectory->remove();
        $this->expectException(PathIsNotDirectoryException::class);
        $this->testDirectory->removeContents();
    }
}
