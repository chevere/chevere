<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\File;

use RuntimeException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\File;
use Chevere\Components\Path\Exceptions\PathIsDirException;
use Chevere\Components\Path\PathApp;
use Chevere\Components\File\Interfaces\FileInterface;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    public function getRealFile(string $filename): FileInterface
    {
        $path = new PathApp('var/FileTest_' . uniqid() . $filename);
        if (false === file_put_contents($path->absolute(), 'una mona pilucha')) {
            throw new RuntimeException('Unable to create file ' . $path->absolute());
        }

        return new File($path);
    }

    public function testWithDirPath(): void
    {
        $path = new PathApp('var');
        $this->expectException(PathIsDirException::class);
        new File($path);
    }

    public function testWithNonExistentPath(): void
    {
        $path = new PathApp('var/FileTest_' . uniqid());
        $file = new File($path);
        $this->assertSame($path, $file->path());
        $this->assertFalse($file->exists());
        $this->assertFalse($file->isPhp());
    }

    public function testWithExistentPath(): void
    {
        $file = $this->getRealFile('.test');
        $this->assertTrue($file->exists());
        if (!unlink($file->path()->absolute())) {
            throw new RuntimeException('Unable to remove file ' . $file->path()->absolute());
        }
    }

    public function testWithPhpPath(): void
    {
        $path = new PathApp('var/FileTest_' . uniqid() . '.php');
        $file = new File($path);
        $this->assertTrue($file->isPhp());
    }

    public function testRemoveNonExistentPath(): void
    {
        $path = new PathApp('var/FileTest_' . uniqid());
        $file = new File($path);
        $this->expectException(FileNotFoundException::class);
        $file->remove();
        $this->assertFalse($file->exists());
    }

    public function testRemoveExistentPath(): void
    {
        $file = $this->getRealFile('.test');
        $file->remove();
        $this->assertFalse($file->exists());
    }

    public function testCreate(): void
    {
        $file = new File(new PathApp('var/FileTest_create'));
        $this->assertFalse($file->exists());
        $file->create();
        $this->assertTrue($file->exists());
        $file->remove();
    }

    public function testPut(): void
    {
        $file = $this->getRealFile('put');
        $id = uniqid();
        $file->put($id);
        $this->assertSame($id, file_get_contents($file->path()->absolute()));
        $file->remove();
    }
}
