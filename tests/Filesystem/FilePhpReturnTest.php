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
use Chevere\Filesystem\Exceptions\FileWithoutContentsException;
use Chevere\Filesystem\File;
use Chevere\Filesystem\FilePhp;
use Chevere\Filesystem\FilePhpReturn;
use Chevere\Filesystem\Interfaces\DirectoryInterface;
use Chevere\Filesystem\Interfaces\FileInterface;
use Chevere\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;
use Chevere\VariableSupport\StorableVariable;
use PHPUnit\Framework\TestCase;
use function Chevere\Filesystem\directoryForPath;
use function Chevere\Filesystem\filePhpReturnForPath;

final class FilePhpReturnTest extends TestCase
{
    private DirectoryInterface $testDirectory;

    private FileInterface $file;

    private FilePhpReturnInterface $filePhpReturn;

    protected function setUp(): void
    {
        $this->testDirectory = directoryForPath(__DIR__ . '/temp/FilePhpReturnTest_' . uniqid() . '/');
        $this->file = new File(
            $this->testDirectory->path()->getChild($this->getFileName())
        );
        $this->file->create();
        $this->filePhpReturn = new FilePhpReturn(
            new FilePhp($this->file)
        );
        $this->assertSame($this->file, $this->filePhpReturn->filePhp()->file());
    }

    protected function tearDown(): void
    {
        $this->testDirectory->removeIfExists();
    }

    public function testConstructFileNotExists(): void
    {
        $filePhp = new FilePhp(
            new File(
                $this->testDirectory->path()->getChild($this->getFileName())
            )
        );
        $return = new FilePhpReturn($filePhp);
        $this->assertInstanceOf(FilePhpReturn::class, $return);
    }

    public function testFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->filePhpReturn->get();
    }

    public function testEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->filePhpReturn->get();
    }

    public function testContents(): void
    {
        $this->file->put(FilePhpReturnInterface::PHP_RETURN . '"test";');
        $this->assertSame('test', $this->filePhpReturn->get());
    }

    public function testVariableFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->filePhpReturn->get();
    }

    public function testVariableEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->filePhpReturn->get();
    }

    public function testVariableInvalidContents(): void
    {
        $this->file->put('test');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->get();
    }

    public function testVariableContents(): void
    {
        $this->file->put(FilePhpReturnInterface::PHP_RETURN . '["test", 1];');
        $this->assertSame(['test', 1], $this->filePhpReturn->get());
    }

    public function testPutFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotExistsException::class);
        $this->filePhpReturn->put(new StorableVariable('test'));
    }

    public function testPhpReturnStringFile(): void
    {
        $phpFilePath = __DIR__ . '/_resources/return-string.php';
        $file = filePhpReturnForPath($phpFilePath);
        $this->assertSame(include $phpFilePath, $file->get());
    }

    public function testPhpReturnObjectFile(): void
    {
        $phpFilePath = __DIR__ . '/_resources/return-object.php';
        $file = filePhpReturnForPath($phpFilePath);
        $phpFileObject = include $phpFilePath;
        $this->assertEquals($phpFileObject, $file->get());
        $this->assertNotSame($phpFileObject, $file->get());
    }

    public function testPut(): void
    {
        foreach ([
            TypeInterface::INTEGER => 1,
            TypeInterface::FLOAT => 1.1,
            TypeInterface::BOOLEAN => true,
            TypeInterface::STRING => 'test',
            TypeInterface::ARRAY => [1, 2, 3],
            TypeInterface::ARRAY => [1, 1.1, true, 'test'],
            TypeInterface::ARRAY => [[1, 1.1, true, 'test']],
        ] as $type => $value) {
            $storable = new StorableVariable($value);
            $this->filePhpReturn->put($storable);
            $this->assertSame($value, $this->filePhpReturn->get());
            $variableType = 'get' . ucfirst($type);
            $this->assertSame($value, $this->filePhpReturn->{$variableType}());
        }
        $object = $this->testDirectory->path()->getChild('test');
        $types = [
            Type::OBJECT => $object,
            Type::ARRAY => ['test', [1, false, $object], 1.1, null],
        ];
        foreach ($types as $type => $value) {
            $storable = new StorableVariable($value);
            $this->filePhpReturn->put($storable);
            $this->assertNotSame($value, $this->filePhpReturn->get());
            $this->assertEquals($value, $this->filePhpReturn->get());
            $variableType = 'get' . ucfirst($type);
            $this->assertEquals($value, $this->filePhpReturn->{$variableType}());
        }
    }

    public function testFileWithoutContentsException(): void
    {
        $this->file->put('');
        $this->expectException(FileWithoutContentsException::class);
        $this->filePhpReturn->get();
    }

    public function testRawVariable(): void
    {
        $this->file->put("<?php /* comment */ return 'test';");
        $string = 'test';
        $this->assertSame($string, $this->filePhpReturn->get());
        $this->assertSame($string, $this->filePhpReturn->get());
        $array = [1, 1.1, 'test'];
        $this->file->put("<?php return [1, 1.1, 'test'];");
        $this->assertSame($array, $this->filePhpReturn->get());
        $this->assertSame($array, $this->filePhpReturn->get());
        $this->file->put('<?php $var = __FILE__;');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->get();
    }

    public function testStrictBegin(): void
    {
        $this->file->put('/* comment */<?php return __FILE__;');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->get();
    }

    public function testStrictEnd(): void
    {
        $this->file->put('<?php return __FILE__;/* comment */');
        $this->expectException(FileInvalidContentsException::class);
        $this->filePhpReturn->get();
    }

    private function getFileName(): string
    {
        return 'var/FileReturnTest_' . uniqid() . '.php';
    }
}
