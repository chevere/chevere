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

use Chevere\Components\Filesystem\File\Exceptions\FileNotFoundException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FileCompile;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\Path\AppPath;
use PHPUnit\Framework\TestCase;

final class FileCompileTest extends TestCase
{
    public function testConstruct(): void
    {
        $file = new File(
            new AppPath('var/FileCompileTest_' . uniqid() . '.php')
        );
        $filePhp = new PhpFile($file);
        $fileCompile = new FileCompile($filePhp);
        $this->assertSame($file, $fileCompile->filePhp()->file());
    }

    public function testCompileFileNotExists(): void
    {
        $file = new File(
            new AppPath('var/FileCompileTest_' . uniqid() . '.php')
        );
        $filePhp = new PhpFile($file);
        $fileCompile = new FileCompile($filePhp);
        $this->expectException(FileNotFoundException::class);
        $fileCompile->compile();
    }

    /**
     * @requires extension zend-opcache
     */
    public function testCompileDestroy(): void
    {
        $this->expectNotToPerformAssertions();
        $file = new File(
            new AppPath('var/FileCompileTest_' . uniqid() . '.php')
        );
        $file->create();
        $filePhp = new PhpFile($file);
        $fileCompile = new FileCompile($filePhp);
        $fileCompile->compile();
        $fileCompile->destroy();
        $file->remove();
    }
}
