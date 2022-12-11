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

use function Chevere\Filesystem\directoryForPath;
use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\PathExistsException;
use Chevere\Filesystem\Exceptions\PathIsDirectoryException;
use Chevere\Filesystem\File;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    private DirectoryInterface $testDirectory;

    protected function setUp(): void
    {
        $this->testDirectory = directoryForPath(__DIR__ . '/temp/FileTest_' . uniqid() . '/');
    }

    protected function tearDown(): void
    {
        $this->testDirectory->removeIfExists();
    }

    public function getTestDirectoryChildFile(string $filename): FileInterface
    {
        $child = $this->testDirectory->path()->getChild($filename);

        return new File($child);
    }

    public function testWithDirectoryPath(): void
    {
        $path = new Path(__DIR__);
        $this->expectException(PathIsDirectoryException::class);
        new File($path);
    }

    public function testWithNonExistentPath(): void
    {
        $path = $this->testDirectory->path();
        $file = new File($path);
        $this->assertSame($path, $file->path());
        $this->assertFalse($file->exists());
        $this->assertFalse($file->isPhp());
        $this->expectException(FileNotExistsException::class);
        $file->put('test');
    }

    public function testWithExistentPath(): void
    {
        $file = $this->getTestDirectoryChildFile('.test');
        $file->create();
        $this->assertSame(FileInterface::CHECKSUM_LENGTH, strlen($file->getChecksum()));
        $this->assertSame(filesize($file->path()->__toString()), $file->getSize());
        $this->assertTrue($file->exists());
        $this->expectException(PathExistsException::class);
        $file->create();
    }

    public function testWithPhpPath(): void
    {
        $file = $this->getTestDirectoryChildFile('.php');
        $this->assertTrue($file->isPhp());
    }

    public function testRemoveNonExistentPath(): void
    {
        $file = $this->getTestDirectoryChildFile('.php');
        $file->removeIfExists();
        $this->expectException(FileNotExistsException::class);
        $file->remove();
    }

    public function testRemoveExistentPath(): void
    {
        $file = $this->getTestDirectoryChildFile('.test');
        $file->create();
        $file->removeIfExists();
        $file->create();
        $file->remove();
        $this->assertFalse($file->exists());
    }

    public function testCreate(): void
    {
        $file = $this->getTestDirectoryChildFile('.create');
        $this->assertFalse($file->exists());
        $file->create();
        $file->createIfNotExists();
        $this->assertTrue($file->exists());
        $file->remove();
        $this->assertFalse($file->exists());
        $file->createIfNotExists();
        $this->assertTrue($file->exists());
    }

    public function testPut(): void
    {
        $file = $this->getTestDirectoryChildFile('.put');
        $file->create();
        $id = uniqid();
        $file->put($id);
        $this->assertSame($id, file_get_contents($file->path()->__toString()));
        $file->remove();
    }

    public function testGetChecksum(): void
    {
        $file = $this->getTestDirectoryChildFile('.checksum');
        $this->expectException(FileNotExistsException::class);
        $file->getChecksum();
    }

    public function testGetSize(): void
    {
        $file = $this->getTestDirectoryChildFile('.size');
        $this->expectException(FileNotExistsException::class);
        $file->getSize();
    }

    public function testGetContents(): void
    {
        $file = $this->getTestDirectoryChildFile('.contents');
        $this->expectException(FileNotExistsException::class);
        $file->getContents();
    }
}
