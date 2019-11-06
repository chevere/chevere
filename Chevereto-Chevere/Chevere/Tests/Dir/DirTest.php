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

namespace Chevere\Tests\Path;

use Chevere\Components\Dir\Dir;
use Chevere\Components\Dir\Exceptions\PathIsFileException;
use Chevere\Components\Dir\Exceptions\PathIsNotDirectoryException;
use Chevere\Components\Path\Path;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DirTest extends TestCase
{
    public function testWithFilePath(): void
    {
        $path = new Path('parameters.php');
        $this->expectException(PathIsFileException::class);
        new Dir($path);
    }

    public function testWithNonExistentPath(): void
    {
        $dir = new Dir(new Path('var/DirTest_' . uniqid()));
        $this->assertFalse($dir->exists());
    }
      
    public function testWithExistentPath(): void
    {
        $dir = new Dir(new Path('var'));
        $this->assertTrue($dir->exists());
    }
      
    public function testCreate(): void
    {
        $dir = new Dir(new Path('var/DirTest_' . uniqid()));
        $dir->create();
        $this->assertTrue($dir->exists());
        if (!rmdir($dir->path()->absolute())) {
            throw new RuntimeException('Unable to remove dir ' . $dir->path()->absolute());
        }
    }

    public function testRemoveNonExistentPath(): void
    {
        $path = new Path('var/DirTest_' . uniqid());
        $dir = new Dir($path);
        $this->expectException(PathIsNotDirectoryException::class);
        $dir->remove();
    }

    public function testRemove(): void
    {
        $dir = new Dir(new Path('var/DirTest_' . uniqid()));
        $dir->create();
        $removed = $dir->remove();
        $this->assertContainsEquals($dir->path()->absolute(), $removed);
        $this->assertFalse($dir->exists());
    }

    public function testRemoveWithContents(): void
    {
        $dirPath = new Path('var/DirTest_' . uniqid());
        $filePath = $dirPath->getChild('file');
        $dir = new Dir($dirPath);
        $dir->create();
        if (false === file_put_contents($filePath->absolute(), 'una mona pilucha')) {
            throw new RuntimeException('Unable to create file ' . $filePath->absolute());
        }
        $removed = $dir->remove();
        $this->assertContainsEquals($dirPath->absolute(), $removed);
        $this->assertContainsEquals($filePath->absolute(), $removed);
    }

    public function testRemoveContents(): void
    {
        $dirPath = new Path('var/DirTest_' . uniqid());
        $filePath = $dirPath->getChild('file');
        $dir = new Dir($dirPath);
        $dir->create();
        if (false === file_put_contents($filePath->absolute(), 'una mona pilucha')) {
            throw new RuntimeException('Unable to create file ' . $filePath->absolute());
        }
        $removed = $dir->removeContents();
        $this->assertNotContainsEquals($dirPath->absolute(), $removed);
        $this->assertContainsEquals($filePath->absolute(), $removed);
        $dir->remove();
    }
}
