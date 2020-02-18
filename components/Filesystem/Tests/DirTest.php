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

namespace Chevere\Components\Filesystem\Dir\Tests;

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Exceptions\Dir\DirExistsException;
use Chevere\Components\Filesystem\Exceptions\Dir\DirUnableToCreateException;
use Chevere\Components\Filesystem\Exceptions\Dir\DirUnableToRemoveException;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Exceptions\Path\PathIsFileException;
use Chevere\Components\Filesystem\Exceptions\Path\PathIsNotDirectoryException;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Throwable;

final class DirTest extends TestCase
{
    private DirInterface $dir;

    protected function setUp(): void
    {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['line'];

        $this->dir = new Dir(new Path(__DIR__ . '/DirTest_' . uniqid() . '_' . $bt));
    }

    protected function tearDown(): void
    {
        try {
            $this->dir->remove();
        } catch (Throwable $e) {
            // $e
        }
    }

    public function testWithFilePath(): void
    {
        $path = new Path(__FILE__);
        $this->expectException(PathIsFileException::class);
        new Dir($path);
    }

    public function testWithNonExistentPath(): void
    {
        $this->assertFalse($this->dir->exists());
    }

    public function testCreate(): void
    {
        $this->dir->create();
        $this->assertTrue($this->dir->exists());
    }

    public function testCreateCreateUnable(): void
    {
        $this->expectException(DirUnableToCreateException::class);
        (new Dir(new Path(__DIR__)))
            ->create();
    }

    public function testRemoveNonExistentPath(): void
    {
        $this->expectException(PathIsNotDirectoryException::class);
        $this->dir->remove();
    }

    public function testRemove(): void
    {
        $this->dir->create();
        $removed = $this->dir->remove();
        $this->assertContainsEquals($this->dir->path()->absolute(), $removed);
        $this->assertFalse($this->dir->exists());
    }

    public function testRemoveContents(): void
    {
        $this->dir->create();
        $childPath = $this->dir->path()->getChild('file');
        $childFile = new File($childPath);
        $childFile->create();
        $removed = $this->dir->removeContents();
        $this->assertNotContainsEquals($this->dir->path()->absolute(), $removed);
        $this->assertContainsEquals($childFile->path()->absolute(), $removed);
    }

    public function testRmdirNotEmpty(): void
    {
        $this->dir->create();
        $child = $this->dir->getChild('child');
        $child->create();
        $this->expectException(DirUnableToRemoveException::class);
        $this->dir->rmdir();
    }
}
