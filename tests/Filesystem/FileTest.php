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

use function Chevere\Filesystem\dirForPath;
use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\PathExistsException;
use Chevere\Filesystem\Exceptions\PathIsDirException;
use Chevere\Filesystem\File;
use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use Throwable;

final class FileTest extends TestCase
{
    private DirInterface $testDir;

    protected function setUp(): void
    {
        $this->testDir = dirForPath(__DIR__ . '/FileTest_' . uniqid() . '/');
    }

    protected function tearDown(): void
    {
        try {
            $this->getTestDirChildFile('.test')->remove();
        } catch (Throwable $e) {
            //$e
        }

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

    public function getTestDirChildFile(string $filename): FileInterface
    {
        $child = $this->testDir->path()->getChild($filename);

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
        $path = $this->testDir->path();
        $file = new File($path);
        $this->assertSame($path, $file->path());
        $this->assertFalse($file->exists());
        $this->assertFalse($file->isPhp());
    }

    public function testWithExistentPath(): void
    {
        $file = $this->getTestDirChildFile('.test');
        $file->create();
        $this->assertSame(FileInterface::CHECKSUM_LENGTH, strlen($file->getChecksum()));
        $this->assertSame(filesize($file->path()->__toString()), $file->getSize());
        $this->assertTrue($file->exists());
        $this->expectException(PathExistsException::class);
        $file->create();
    }

    public function testWithPhpPath(): void
    {
        $file = $this->getTestDirChildFile('.php');
        $this->assertTrue($file->isPhp());
    }

    public function testRemoveNonExistentPath(): void
    {
        $file = $this->getTestDirChildFile('.php');
        $file->removeIfExists();
        $this->expectException(FileNotExistsException::class);
        $file->remove();
    }

    public function testRemoveExistentPath(): void
    {
        $file = $this->getTestDirChildFile('.test');
        $file->create();
        $file->removeIfExists();
        $file->create();
        $file->remove();
        $this->assertFalse($file->exists());
    }

    public function testCreate(): void
    {
        $file = $this->getTestDirChildFile('.create');
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
        $file = $this->getTestDirChildFile('.put');
        $file->create();
        $id = uniqid();
        $file->put($id);
        $this->assertSame($id, file_get_contents($file->path()->__toString()));
        $file->remove();
    }

    public function testGetChecksum(): void
    {
        $file = $this->getTestDirChildFile('.checksum');
        $this->expectException(FileNotExistsException::class);
        $file->getChecksum();
    }

    public function testGetSize(): void
    {
        $file = $this->getTestDirChildFile('.size');
        $this->expectException(FileNotExistsException::class);
        $file->getSize();
    }

    public function testGetContents(): void
    {
        $file = $this->getTestDirChildFile('.contents');
        $this->expectException(FileNotExistsException::class);
        $file->getContents();
    }
}
