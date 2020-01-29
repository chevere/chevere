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

namespace Chevere\Components\File\Tests;

use Chevere\Components\Dir\Dir;
use Chevere\Components\Dir\Interfaces\DirInterface;
use RuntimeException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\File;
use Chevere\Components\Path\Exceptions\PathIsDirException;
use Chevere\Components\Path\Path;
use Chevere\Components\File\Interfaces\FileInterface;
use Chevere\Components\Path\Interfaces\PathInterface;
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
        $this->assertTrue($file->exists());
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
