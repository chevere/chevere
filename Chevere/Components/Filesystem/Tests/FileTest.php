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

namespace Chevere\Components\Filesystem\Tests;

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Exceptions\File\FileExistsException;
use RuntimeException;
use Chevere\Components\Filesystem\Exceptions\File\FileNotFoundException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Exceptions\Path\PathIsDirException;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use PHPUnit\Framework\TestCase;
use Throwable;

final class FileTest extends TestCase
{
    private DirInterface $dir;

    protected function setUp(): void
    {
        $this->dir = new Dir(new Path(__DIR__ . '/FileTest_' . uniqid()));
    }

    protected function tearDown(): void
    {
        try {
            $this->dir->remove();
        } catch (Throwable $e) {
            //$e
        }
    }

    public function getChildFile(string $filename): FileInterface
    {
        $child = $this->dir->path()->getChild($filename);

        return new File($child);
    }

    public function testWithDirPath(): void
    {
        $path = new Path(__DIR__);
        $this->expectException(PathIsDirException::class);
        new File($path);
    }

    public function testWithNonExistentPath(): void
    {
        $path = $this->dir->path();
        $file = new File($path);
        $this->assertSame($path, $file->path());
        $this->assertFalse($file->exists());
        $this->assertFalse($file->isPhp());
    }

    public function testWithExistentPath(): void
    {
        $file = $this->getChildFile('.test');
        $file->create();
        $this->assertSame(FileInterface::CHECKSUM_LENGTH, strlen($file->checksum()));
        $this->assertTrue($file->exists());
        $this->expectException(FileExistsException::class);
        $file->create();
    }

    public function testWithPhpPath(): void
    {
        $file = $this->getChildFile('.php');
        $this->assertTrue($file->isPhp());
    }

    public function testRemoveNonExistentPath(): void
    {
        $file = $this->getChildFile('.php');
        $this->expectException(FileNotFoundException::class);
        $file->remove();
    }

    public function testRemoveExistentPath(): void
    {
        $file = $this->getChildFile('.test');
        $file->create();
        $file->remove();
        $this->assertFalse($file->exists());
    }

    public function testCreate(): void
    {
        $file = $this->getChildFile('.create');
        $this->assertFalse($file->exists());
        $file->create();
        $this->assertTrue($file->exists());
    }

    public function testPut(): void
    {
        $file = $this->getChildFile('put');
        $file->create();
        $id = uniqid();
        $file->put($id);
        $this->assertSame($id, file_get_contents($file->path()->absolute()));
        $file->remove();
    }
}
