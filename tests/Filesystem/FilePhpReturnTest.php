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

use Chevere\Filesystem\Exceptions\FileInvalidContentsException;
use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileReturnInvalidTypeException;
use Chevere\Filesystem\Exceptions\FileWithoutContentsException;
use Chevere\Filesystem\File;
use Chevere\Filesystem\FilePhp;
use Chevere\Filesystem\FilePhpReturn;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\Filesystem\Interfaces\PathInterface;
use Chevere\Filesystem\Path;
use Chevere\Type\Type;
use Chevere\VarSupport\VarStorable;
use PHPUnit\Framework\TestCase;

final class FilePhpReturnTest extends TestCase
{
    private PathInterface $path;

    private FileInterface $file;

    private FilePhpReturnInterface $filePhpReturn;

    protected function setUp(): void
    {
        $this->path = new Path(__DIR__ . '/_resources/FileReturnTest/');
        $this->file = new File(
            $this->path->getChild($this->getFileName())
        );
        $this->file->create();
        $this->filePhpReturn = new FilePhpReturn(
            new FilePhp($this->file)
        );
        $this->assertSame($this->file, $this->filePhpReturn->filePhp()->file());
    }

    protected function tearDown(): void
    {
        if ($this->file->exists()) {
            $this->file->remove();
        }
    }

    public function testConstructFileNotExists(): void
    {
        $filePhp = new FilePhp(
            new File(
                $this->path->getChild($this->getFileName())
            )
        );
        $return = new FilePhpReturn($filePhp);
        $this->assertInstanceOf(FilePhpReturn::class, $return);
    }

    public function testFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->filePhpReturn->raw();
    }

    public function testEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->filePhpReturn->raw();
    }

    public function testContents(): void
    {
        $this->file->put(FilePhpReturnInterface::PHP_RETURN . '"test";');
        $this->assertSame('test', $this->filePhpReturn->raw());
    }

    public function testVarFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->filePhpReturn->var();
    }

    public function testVarEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->filePhpReturn->var();
    }

    public function testVarInvalidContents(): void
    {
        $this->file->put('test');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->var();
    }

    public function testVarContents(): void
    {
        $this->file->put(FilePhpReturnInterface::PHP_RETURN . '["test", 1];');
        $this->assertSame(['test', 1], $this->filePhpReturn->var());
    }

    public function testPutFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->filePhpReturn->put(new VarStorable('test'));
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
            $this->filePhpReturn->put(
                new VarStorable($val)
            );
            $this->assertSame($val, $this->filePhpReturn->var());
        }

        $types = [
            Type::OBJECT => $this->path->getChild('test'),
            Type::ARRAY => ['test', [1, false], 1.1, null],
        ];
        foreach ($types as $type => $val) {
            $this->filePhpReturn->put(
                new VarStorable($val)
            );
            $this->assertEqualsCanonicalizing(
                $val,
                $this->filePhpReturn->var()
            );
            $this->assertEqualsCanonicalizing(
                $val,
                $this->filePhpReturn->varType(new Type($type))
            );
        }
        $this->expectException(FileReturnInvalidTypeException::class);
        $this->filePhpReturn->varType(new Type(Type::INTEGER));
    }

    public function testFileWithoutContentsException(): void
    {
        $this->file->put('');
        $this->expectException(FileWithoutContentsException::class);
        $this->filePhpReturn->raw();
    }

    public function testRawVar(): void
    {
        $this->file->put("<?php /* comment */ return 'test';");
        $string = 'test';
        $this->assertSame($string, $this->filePhpReturn->raw());
        $this->assertSame($string, $this->filePhpReturn->var());
        $array = [1, 1.1, 'test'];
        $this->file->put("<?php return [1, 1.1, 'test'];");
        $this->assertSame($array, $this->filePhpReturn->raw());
        $this->assertSame($array, $this->filePhpReturn->var());
        $this->file->put('<?php $var = __FILE__;');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->raw();
    }

    public function testStrictBegin(): void
    {
        $this->file->put('/* comment */<?php return __FILE__;');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->raw();
    }

    public function testStrictEnd(): void
    {
        $this->file->put('<?php return __FILE__;/* comment */');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->raw();
    }

    private function getFileName(): string
    {
        return 'var/FileReturnTest_' . uniqid() . '.php';
    }
}
