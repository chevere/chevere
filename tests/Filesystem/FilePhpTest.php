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

use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileNotPhpException;
use Chevere\Filesystem\File;
use Chevere\Filesystem\FilePhp;
use Chevere\Filesystem\Interfaces\PathInterface;
use Chevere\Filesystem\Path;
use PHPUnit\Framework\TestCase;

final class FilePhpTest extends TestCase
{
    private PathInterface $path;

    protected function setUp(): void
    {
        $this->path = new Path(__DIR__ . '/_resources/FilePhpTest/');
    }

    public function testNotPhpFile(): void
    {
        $file = new File(
            $this->path->getChild('var/FilePhpTest_' . uniqid())
        );
        $this->expectException(FileNotPhpException::class);
        new FilePhp($file);
    }

    public function testConstructor(): void
    {
        $file = new File(
            $this->path->getChild('var/FilePhpTest_' . uniqid() . '.php')
        );
        $filePhp = new FilePhp($file);
        $this->assertSame($file, $filePhp->file());
    }

    public function testCompileFileNotExists(): void
    {
        $file = new File(
            $this->path->getChild('var/FilePhpTest_' . uniqid() . '.php')
        );
        $filePhp = new FilePhp($file);
        $this->expectException(FileNotExistsException::class);
        $filePhp->compileCache();
    }

    /**
     * @requires extension Zend OPcache
     */
    public function testCompileDestroy(): void
    {
        if (opcache_get_status() === false) {
            $this->markTestSkipped('OPCache is not enabled');
        }
        $this->expectNotToPerformAssertions();
        $file = new File(
            $this->path->getChild('var/FilePhpTest_' . uniqid() . '.php')
        );
        $file->create();
        $filePhp = new FilePhp($file);
        $filePhp->compileCache();
        $filePhp->flushCache();
        $file->remove();
    }
}
