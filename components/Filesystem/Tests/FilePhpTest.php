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

use Chevere\Components\Filesystem\Exceptions\File\FileNotPhpException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Filesystem\Exceptions\File\FileNotFoundException;
use PHPUnit\Framework\TestCase;

final class FilePhpTest extends TestCase
{
    public function testNotPhpFile(): void
    {
        $file = new File(
            new AppPath('var/FilePhpTest_' . uniqid())
        );
        $this->expectException(FileNotPhpException::class);
        new PhpFile($file);
    }

    public function testConstructor(): void
    {
        $file = new File(
            new AppPath('var/FilePhpTest_' . uniqid() . '.php')
        );
        $filePhp = new PhpFile($file);
        $this->assertSame($file, $filePhp->file());
        // $this->assertIsBool($filePhp->isCompileable());
    }

    public function testCompileFileNotExists(): void
    {
        $file = new File(
            new AppPath('var/FilePhpTest_' . uniqid() . '.php')
        );
        $filePhp = new PhpFile($file);
        $this->expectException(FileNotFoundException::class);
        $filePhp->cache();
    }

    /**
     * @requires extension Zend OPcache
     */
    public function testCompileDestroy(): void
    {
        $this->expectNotToPerformAssertions();
        $file = new File(
            new AppPath('var/FilePhpTest_' . uniqid() . '.php')
        );
        $file->create();
        $filePhp = new PhpFile($file);
        $filePhp->cache();
        $filePhp->flush();
        $file->remove();
    }
}
