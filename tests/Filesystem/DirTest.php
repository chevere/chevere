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

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Filesystem\Path;
use Chevere\Exceptions\Filesystem\DirTailException;
use Chevere\Exceptions\Filesystem\DirUnableToCreateException;
use Chevere\Exceptions\Filesystem\PathIsFileException;
use Chevere\Exceptions\Filesystem\PathIsNotDirectoryException;
use Chevere\Interfaces\Filesystem\DirInterface;
use PHPUnit\Framework\TestCase;
use Throwable;

final class DirTest extends TestCase
{
    private DirInterface $testDir;

    protected function setUp(): void
    {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['line'];

        $this->testDir = (new FilesystemFactory)->getDirFromString(
            __DIR__ . '/DirTest_' . uniqid() . '_' . $bt . '/'
        );
    }

    protected function tearDown(): void
    {
        try {
            $this->testDir->remove();
        } catch (Throwable $e) {
            // $e
        }
    }

    public function testInvalidPath(): void
    {
        $this->expectException(DirTailException::class);
        (new FilesystemFactory)->getDirFromString(__DIR__);
    }

    public function testWithFilePath(): void
    {
        $path = new Path(__FILE__);
        $this->expectException(PathIsFileException::class);
        new Dir($path);
    }

    public function testWithNonExistentPath(): void
    {
        $this->assertFalse($this->testDir->exists());
    }

    public function testCreate(): void
    {
        $this->testDir->create();
        $this->assertTrue($this->testDir->exists());
    }

    public function testCreateCreateUnable(): void
    {
        $this->expectException(DirUnableToCreateException::class);
        (new FilesystemFactory)
            ->getDirFromString(__DIR__ . '/')
            ->create();
    }

    public function testRemoveNonExistentPath(): void
    {
        $this->expectException(PathIsNotDirectoryException::class);
        $this->testDir->remove();
    }

    public function testRemove(): void
    {
        $this->testDir->create();
        $removed = $this->testDir->remove();
        $this->assertContainsEquals($this->testDir->path()->absolute(), $removed);
        $this->assertFalse($this->testDir->exists());
    }

    public function testRemoveContents(): void
    {
        $this->testDir->create();
        $childPath = $this->testDir->path()->getChild('file');
        $childFile = new File($childPath);
        $childFile->create();
        $removed = $this->testDir->removeContents();
        $this->assertNotContainsEquals($this->testDir->path()->absolute(), $removed);
        $this->assertContainsEquals($childFile->path()->absolute(), $removed);
    }
}
