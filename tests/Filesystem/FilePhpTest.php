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

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\Path;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Chevere\Exceptions\Filesystem\FileNotPhpException;
use Chevere\Interfaces\Filesystem\PathInterface;
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
        $filePhp->cache();
    }

    /**
     * @requires extension Zend OPcache
     */
    public function testCompileDestroy(): void
    {
        $this->expectNotToPerformAssertions();
        $file = new File(
            $this->path->getChild('var/FilePhpTest_' . uniqid() . '.php')
        );
        $file->create();
        $filePhp = new FilePhp($file);
        $filePhp->cache();
        $filePhp->flush();
        $file->remove();
    }
}
