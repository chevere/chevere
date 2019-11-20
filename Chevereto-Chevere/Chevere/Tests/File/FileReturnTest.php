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

use Chevere\Components\File\Exceptions\FileInvalidContentsException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileWithoutContentsException;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\File\FileReturn;
use Chevere\Components\Path\Path;
use Chevere\Components\Variable\VariableExportable;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FileReturnContract;
use PHPUnit\Framework\TestCase;

final class FileReturnTest extends TestCase
{
    /** @var FileContract */
    private $file;

    /** @var FileReturnContract */
    private $fileReturn;

    private function getFileName(): string
    {
        return 'var/FileReturnTest_' . uniqid() . '.php';
    }

    public function setUp(): void
    {
        $this->file = new File(
            new Path($this->getFileName())
        );
        $this->file->create();
        $this->fileReturn = new FileReturn(
            new FilePhp($this->file)
        );
        $this->assertSame($this->file, $this->fileReturn->file());
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
            new FilePhp(
                new File(
                    new Path($this->getFileName())
                )
            )
        );
    }

    public function testReturnFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->fileReturn->return();
    }

    public function testReturnEmptyFile(): void
    {
        $this->expectException(FileWithoutContentsException::class);
        $this->fileReturn
            ->return();
    }

    public function testReturnInvalidContents(): void
    {
        $this->file->put('<?php return "test";');
        $this->expectException(FileInvalidContentsException::class);
        $this->fileReturn->return();
    }

    public function testReturnContents(): void
    {
        $this->file->put(FileReturnContract::PHP_RETURN . '"test";');
        $this->assertSame('test', $this->fileReturn->return());
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
        $this->file->put(FileReturnContract::PHP_RETURN . '["test", 1];');
        $this->assertSame(['test', 1], $this->fileReturn->var());
    }

    public function testPutFileNotFound(): void
    {
        $this->file->remove();
        $this->expectException(FileNotFoundException::class);
        $this->fileReturn->put(new VariableExportable('test'));
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
                new VariableExportable($val)
            );
            $this->assertSame($val, $this->fileReturn->var());
        }

        foreach ([
            new Path('test'),
            ['test', [1, false], new Path('test')],
        ] as $val) {
            $this->fileReturn->put(
                new VariableExportable($val)
            );
            $this->assertEquals($val, $this->fileReturn->var());
        }
    }

    public function testWithNoStrict(): void
    {
        $this->file->put("<?php return 'test';");
        $this->fileReturn = $this->fileReturn
            ->withNoStrict();

        $string = 'test';
        $this->assertSame($string, $this->fileReturn->return());
        $this->assertSame($string, $this->fileReturn->var());

        $array = [1, 1.1, 'test'];
        $this->file->put("<?php return [1, 1.1, 'test'];");
        $this->assertSame($array, $this->fileReturn->return());
        $this->assertSame($array, $this->fileReturn->var());
    }
}
