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

use Chevere\Components\File\Exceptions\FileNotPhpException;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Path\Path;
use PHPUnit\Framework\TestCase;

final class FilePhpTest extends TestCase
{
    public function testNotPhpFile(): void
    {
        $file = new File(
            new Path('var/FileReturnTest_' . uniqid())
        );
        $this->expectException(FileNotPhpException::class);
        new FilePhp($file);
    }

    public function testConstructor(): void
    {
        $file = new File(
            new Path('var/FileReturnTest_' . uniqid() . '.php')
        );
        $filePhp = new FilePhp($file);
        $this->assertSame($file, $filePhp->file());
    }
}
