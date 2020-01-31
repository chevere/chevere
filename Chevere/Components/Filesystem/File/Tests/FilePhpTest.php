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

use Chevere\Components\Filesystem\File\Exceptions\FileNotPhpException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\Path\AppPath;
use PHPUnit\Framework\TestCase;

final class FilePhpTest extends TestCase
{
    public function testNotPhpFile(): void
    {
        $file = new File(
            new AppPath('var/FileReturnTest_' . uniqid())
        );
        $this->expectException(FileNotPhpException::class);
        new PhpFile($file);
    }

    public function testConstructor(): void
    {
        $file = new File(
            new AppPath('var/FileReturnTest_' . uniqid() . '.php')
        );
        $filePhp = new PhpFile($file);
        $this->assertSame($file, $filePhp->file());
    }
}
