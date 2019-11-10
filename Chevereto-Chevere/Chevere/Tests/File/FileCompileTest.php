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

use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileNotPhpException;
use Chevere\Components\File\File;
use Chevere\Components\File\FileCompile;
use Chevere\Components\Path\Path;
use PHPUnit\Framework\TestCase;

final class FileCompileTest extends TestCase
{
    public function testFileNotPhp(): void
    {
        $path = new Path('var/FileCompileTest_' . uniqid());
        $file = new File($path);
        $this->expectException(FileNotPhpException::class);
        new FileCompile($file);
    }

    public function testFileNotExists(): void
    {
        $path = new Path('var/FileCompileTest_' . uniqid() . '.php');
        $file = new File($path);
        $this->expectException(FileNotFoundException::class);
        new FileCompile($file);
    }

    public function testCompileDestroy(): void
    {
        $this->expectNotToPerformAssertions();
        $path = new Path('var/FileCompileTest_' . uniqid() . '.php');
        $file = new File($path);
        $file->create();
        $compile = new FileCompile($file);
        $compile->compile();
        $compile->destroy();
    }
}
