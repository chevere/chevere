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

final class FileReturnTest extends TestCase
{
    private PathInterface $path;

    private FileInterface $file;

    private FilePhpReturnInterface $phpFileReturn;

    protected function setUp(): void
    {
        $this->path = new Path(__DIR__ . '/_resources/FileReturnTest/');
        $this->file = new File(
            $this->path->getChild($this->getFileName())
        );
        $this->file->create();
        $this->phpFileReturn = new FilePhpReturn(
            new FilePhp($this->file)
        );
        $this->assertSame($this->file, $this->phpFileReturn->filePhp()->file());
    }

    protected function tearDown(): void
    {
        if ($this->file->exists()) {
            $this->file->remove();
        }
    }

    public function testConstructFileNotFound(): void
    {
        $filePhp = new FilePhp(
            new File(
                $this->path->getChild($this->getFileName())
            )
        );
        $this->expectException(FileNotExistsException::class);
        new FilePhpReturn($filePhp);
    }

    public function testFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->phpFileReturn->raw();
    }

    public function testEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->phpFileReturn
            ->raw();
    }

    public function testContents(): void
    {
        $this->file->put(FilePhpReturnInterface::PHP_RETURN . '"test";');
        $this->assertSame('test', $this->phpFileReturn->raw());
    }

    public function testVarFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
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
        $this->file->put(FilePhpReturnInterface::PHP_RETURN . '["test", 1];');
        $this->assertSame(['test', 1], $this->phpFileReturn->var());
    }

    public function testPutFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->phpFileReturn->put(new VarStorable('test'));
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
                new VarStorable($val)
            );
            $this->assertSame($val, $this->phpFileReturn->var());
        }

        $types = [
            Type::OBJECT => $this->path->getChild('test'),
            Type::ARRAY => ['test', [1, false], 1.1, null],
        ];
        foreach ($types as $type => $val) {
            $this->phpFileReturn->put(
                new VarStorable($val)
            );
            $this->assertEqualsCanonicalizing(
                $val,
                $this->phpFileReturn->var()
            );
            $this->assertEqualsCanonicalizing(
                $val,
                $this->phpFileReturn->varType(new Type($type))
            );
        }
        $this->expectException(FileReturnInvalidTypeException::class);
        $this->phpFileReturn->varType(new Type(Type::INTEGER));
    }

    public function testFileWithoutContentsException(): void
    {
        $this->file->put('');
        $this->expectException(FileWithoutContentsException::class);
        $this->phpFileReturn->raw();
    }

    public function testWithNoStrict(): void
    {
        $this->file->put("<?php /* comment */ return 'test';");
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

    private function getFileName(): string
    {
        return 'var/FileReturnTest_' . uniqid() . '.php';
    }
}
