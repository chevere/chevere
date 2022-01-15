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

use function Chevere\Components\Filesystem\dirForPath;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Path;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Chevere\Exceptions\Filesystem\PathExistsException;
use Chevere\Exceptions\Filesystem\PathIsDirException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\FileInterface;
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
            $this->getChildFile('.test')->remove();
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

    public function getChildFile(string $filename): FileInterface
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
        $file = $this->getChildFile('.test');
        $file->create();
        $this->assertSame(FileInterface::CHECKSUM_LENGTH, strlen($file->getChecksum()));
        $this->assertSame(filesize($file->path()->toString()), $file->getSize());
        $this->assertTrue($file->exists());
        $this->expectException(PathExistsException::class);
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
        $file->removeIfExists();
        $this->expectException(FileNotExistsException::class);
        $file->remove();
    }

    public function testRemoveExistentPath(): void
    {
        $file = $this->getChildFile('.test');
        $file->create();
        $file->removeIfExists();
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
        $file->remove();
    }

    public function testPut(): void
    {
        $file = $this->getChildFile('.put');
        $file->create();
        $id = uniqid();
        $file->put($id);
        $this->assertSame($id, file_get_contents($file->path()->toString()));
        $file->remove();
    }

    public function testGetChecksum(): void
    {
        $file = $this->getChildFile('.checksum');
        $this->expectException(FileNotExistsException::class);
        $file->getChecksum();
    }

    public function testGetSize(): void
    {
        $file = $this->getChildFile('.size');
        $this->expectException(FileNotExistsException::class);
        $file->getSize();
    }

    public function testGetContents(): void
    {
        $file = $this->getChildFile('.contents');
        $this->expectException(FileNotExistsException::class);
        $file->getContents();
    }
}
