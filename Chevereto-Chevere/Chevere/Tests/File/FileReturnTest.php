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

use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\File\FileReturn;
use Chevere\Components\Path\Path;
use PHPUnit\Framework\TestCase;

final class FileReturnTest extends TestCase
{
    public function testConstructor(): void
    {
        $file = new File(
            new Path('var/FileReturnTest_' . uniqid() . '.php')
        );
        $fileReturn = new FileReturn(
            new FilePhp($file)
        );
        $this->assertSame($file, $fileReturn->file());
    }

    public function testEmptyFile(): void
    {
    }

    public function testWithStrict(): void
    {
    }
}
