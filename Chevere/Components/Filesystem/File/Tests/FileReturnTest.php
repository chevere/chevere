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

use Chevere\Components\Filesystem\File\Exceptions\FileInvalidContentsException;
use Chevere\Components\Filesystem\File\Exceptions\FileNotFoundException;
use Chevere\Components\Filesystem\File\Exceptions\FileWithoutContentsException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\FileReturn;
use Chevere\Components\Filesystem\Path\AppPath;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Filesystem\Interfaces\FileInterface;
use Chevere\Components\Filesystem\Interfaces\FileReturnInterface;
use PHPUnit\Framework\TestCase;

final class FileReturnTest extends TestCase
{
    /** @var FileInterface */
    private $file;

    /** @var FileReturnInterface */
    private $fileReturn;

    private function getFileName(): string
    {
        return 'var/FileReturnTest_' . uniqid() . '.php';
    }

    public function setUp(): void
    {
        $this->file = new File(
            new AppPath($this->getFileName())
        );
        $this->file->create();
        $this->fileReturn = new FileReturn(
            new PhpFile($this->file)
        );
        $this->assertSame($this->file, $this->fileReturn->filePhp()->file());
    }

    public function tearDown(): void
    {
        if ($this->file->exists()) {
            $this->file->remove();
        }
    }

    public function testConstructFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        new FileReturn(
            new PhpFile(
                new File(
                    new AppPath($this->getFileName())
                )
            )
        );
    }

    public function testReturnFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->fileReturn->raw();
    }

    public function testReturnEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->fileReturn
            ->raw();
    }

    public function testReturnInvalidContents(): void
    {
        $this->file->put('<?php\n\nreturn "test";');
        $this->expectException(FileInvalidContentsException::class);
        $this->fileReturn->raw();
    }

    public function testReturnContents(): void
    {
        $this->file->put(FileReturnInterface::PHP_RETURN . '"test";');
        $this->assertSame('test', $this->fileReturn->raw());
    }

    public function testVarFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->fileReturn->var();
    }

    public function testVarEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->fileReturn
            ->var();
    }

    public function testVarInvalidContents(): void
    {
        $this->file->put('test');
        $this->expectException(FileInvalidContentsException::class);
        $this->fileReturn->var();
    }

    public function testVarContents(): void
    {
        $this->file->put(FileReturnInterface::PHP_RETURN . '["test", 1];');
        $this->assertSame(['test', 1], $this->fileReturn->var());
    }

    public function testPutFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->fileReturn->put(new VariableExport('test'));
    }

    public function testPut(): void
    {
        foreach ([
            1,
            1.1,
            true,
            'test',
            [1, 2, 3],
            [1, 1.1, true, 'test'],
            [[1, 1.1, true, 'test']],
        ] as $val) {
            $this->fileReturn->put(
                new VariableExport($val)
            );
            $this->assertSame($val, $this->fileReturn->var());
        }

        foreach ([
            new AppPath('test'),
            ['test', [1, false], new AppPath('test')],
        ] as $val) {
            $this->fileReturn->put(
                new VariableExport($val)
            );
            $this->assertEquals($val, $this->fileReturn->var());
        }
    }

    public function testWithNoStrict(): void
    {
        $this->file->put("<?php /* comment */ return 'test';");
        $this->fileReturn = $this->fileReturn
            ->withNoStrict();

        $string = 'test';
        $this->assertSame($string, $this->fileReturn->raw());
        $this->assertSame($string, $this->fileReturn->var());

        $array = [1, 1.1, 'test'];
        $this->file->put("<?php return [1, 1.1, 'test'];");
        $this->assertSame($array, $this->fileReturn->raw());
        $this->assertSame($array, $this->fileReturn->var());
    }
}
