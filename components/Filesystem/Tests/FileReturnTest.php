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

use Chevere\Components\Filesystem\Exceptions\File\FileInvalidContentsException;
use Chevere\Components\Filesystem\Exceptions\File\FileNotFoundException;
use Chevere\Components\Filesystem\Exceptions\File\FileWithoutContentsException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\File\PhpFileReturnInterface;
use PHPUnit\Framework\TestCase;

final class FileReturnTest extends TestCase
{
    /** @var FileInterface */
    private FileInterface $file;

    /** @var PhpFileReturnInterface */
    private PhpFileReturnInterface $phpFileReturn;

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
        $this->phpFileReturn = new PhpFileReturn(
            new PhpFile($this->file)
        );
        $this->assertSame($this->file, $this->phpFileReturn->filePhp()->file());
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
        new PhpFileReturn(
            new PhpFile(
                new File(
                    new AppPath($this->getFileName())
                )
            )
        );
    }

    public function testFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->phpFileReturn->raw();
    }

    public function testEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->phpFileReturn
            ->raw();
    }

    public function testInvalidContents(): void
    {
        $this->file->put('<?php\n\nreturn "test";');
        $this->expectException(FileInvalidContentsException::class);
        $this->phpFileReturn->raw();
    }

    public function testContents(): void
    {
        $this->file->put(PhpFileReturnInterface::PHP_RETURN . '"test";');
        $this->assertSame('test', $this->phpFileReturn->raw());
    }

    public function testVarFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->phpFileReturn->var();
    }

    public function testVarEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->phpFileReturn->var();
    }

    public function testVarInvalidContents(): void
    {
        $this->file->put('test');
        $this->expectException(FileInvalidContentsException::class);
        $this->phpFileReturn->var();
    }

    public function testVarContents(): void
    {
        $this->file->put(PhpFileReturnInterface::PHP_RETURN . '["test", 1];');
        $this->assertSame(['test', 1], $this->phpFileReturn->var());
    }

    public function testPutFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->phpFileReturn->put(new VariableExport('test'));
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
            $this->phpFileReturn->put(
                new VariableExport($val)
            );
            $this->assertSame($val, $this->phpFileReturn->var());
        }

        foreach ([
            new AppPath('test'),
            ['test', [1, false], new AppPath('test')],
        ] as $val) {
            $this->phpFileReturn->put(
                new VariableExport($val)
            );
            $this->assertEquals($val, $this->phpFileReturn->var());
        }
    }

    public function testFileWithoutContentsException(): void
    {
        $this->file->put('');
        $this->phpFileReturn = $this->phpFileReturn->withStrict(false);
        $this->expectException(FileWithoutContentsException::class);
        $this->phpFileReturn->raw();
    }

    public function testWithNoStrict(): void
    {
        $this->file->put("<?php /* comment */ return 'test';");
        $this->phpFileReturn = $this->phpFileReturn->withStrict(false);
        $string = 'test';
        $this->assertSame($string, $this->phpFileReturn->raw());
        $this->assertSame($string, $this->phpFileReturn->var());
        $array = [1, 1.1, 'test'];
        $this->file->put("<?php return [1, 1.1, 'test'];");
        $this->assertSame($array, $this->phpFileReturn->raw());
        $this->assertSame($array, $this->phpFileReturn->var());
        $this->file->put('<?php $var = __FILE__;');
        $this->expectException(FileInvalidContentsException::class);
        $this->phpFileReturn->raw();
    }
}
