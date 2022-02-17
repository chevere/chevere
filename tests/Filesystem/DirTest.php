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
use Chevere\Filesystem\Exceptions\DirExistsException;
use Chevere\Filesystem\Exceptions\DirNotExistsException;
use Chevere\Filesystem\Exceptions\PathIsFileException;
use Chevere\Filesystem\Exceptions\PathIsNotDirectoryException;
use Chevere\Filesystem\Exceptions\PathTailException;
use Chevere\Filesystem\File;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use Throwable;

final class DirTest extends TestCase
{
    private DirInterface $testDir;

    protected function setUp(): void
    {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['line'];

        $this->testDir = new Dir(
            new Path(__DIR__ . '/DirTest_' . uniqid() . '_' . $bt . '/')
        );
    }

    protected function tearDown(): void
    {
        try {
            $this->testDir->remove();
        } catch (Throwable) {
            // Ignore fs issues
        }
    }

    public function testWithFilePath(): void
    {
        $path = new Path(__DIR__ . '/no-tail');
        $this->expectException(PathTailException::class);
        new Dir($path);
    }

    public function testWithActualFilePath(): void
    {
        $path = new Path(__FILE__);
        $this->expectException(PathIsFileException::class);
        new Dir($path);
    }

    public function testNotExists(): void
    {
        $this->assertFalse($this->testDir->getChild('not-exists/')->exists());
        $this->assertSame(
            [],
            $this->testDir->getChild('not-exists/')->removeIfExists()
        );
    }

    public function testAssertExists(): void
    {
        $this->expectException(DirNotExistsException::class);
        $this->testDir->getChild('not-exists/')->assertExists();
    }

    public function testCreate(): void
    {
        $this->testDir->create();
        $this->testDir->assertExists();
        $this->assertTrue($this->testDir->exists());
        $this->testDir->removeIfExists();
        $this->assertFalse($this->testDir->exists());
    }

    public function testCreateIfNotExists(): void
    {
        $this->testDir->createIfNotExists();
        $this->testDir->createIfNotExists();
        $this->assertTrue($this->testDir->exists());
    }

    public function testCreateDirExists(): void
    {
        $this->expectException(DirExistsException::class);
        (new Dir(new Path(__DIR__ . '/')))->create();
    }

    public function testRemoveNonExistentPath(): void
    {
        $this->expectException(PathIsNotDirectoryException::class);
        $this->testDir->remove();
    }

    public function testRemove(): void
    {
        $this->testDir->create();
        $this->testDir->getChild('child/')->create();
        $removed = $this->testDir->remove();
        $this->assertContainsEquals($this->testDir->path()->__toString(), $removed);
        $this->assertFalse($this->testDir->exists());
        $this->expectException(PathIsNotDirectoryException::class);
        $this->testDir->remove();
    }

    public function testRemoveContents(): void
    {
        $this->testDir->create();
        $childDir1 = new Dir($this->testDir->path()->getChild('dir1/'));
        $childFile1 = new File($this->testDir->path()->getChild('file1'));
        $childFile2 = new File($this->testDir->path()->getChild('file2'));
        $childDir1->create();
        $childFile1->create();
        $childFile2->create();
        $removed = $this->testDir->removeContents();
        $this->assertCount(3, $removed);
        $this->assertNotContainsEquals($this->testDir->path()->__toString(), $removed);
        $this->assertContainsEquals($childFile1->path()->__toString(), $removed);
        $this->assertContainsEquals($childFile1->path()->__toString(), $removed);
        $this->assertContainsEquals($childFile2->path()->__toString(), $removed);
        $this->testDir->remove();
        $this->expectException(PathIsNotDirectoryException::class);
        $this->testDir->removeContents();
    }
}
