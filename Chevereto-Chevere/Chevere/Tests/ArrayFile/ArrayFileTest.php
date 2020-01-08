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

namespace Chevere\Tests\ArrayFile;

use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\ArrayFile\Exceptions\ArrayFileTypeException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileReturnInvalidTypeException;
use Chevere\Components\File\Exceptions\FileWithoutContentsException;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Path\PathApp;
use Chevere\Components\Type\Type;
use Chevere\Components\File\Contracts\FileContract;
use Chevere\Contracts\Path\PathContract;
use PHPUnit\Framework\TestCase;
use TypeError;

final class ArrayFileTest extends TestCase
{
    /** @var FileContract */
    private $file;

    public function setUp(): void
    {
        $this->file = new File(
            new PathApp('var/ArrayFileTest_' . uniqid() . '.php')
        );
    }

    public function tearDown(): void
    {
        if ($this->file->exists()) {
            $this->file->remove();
        }
    }

    public function testConstructWithNotFoundFilePhp(): void
    {
        $filePhp = new FilePhp($this->file);
        $this->expectException(FileNotFoundException::class);
        new ArrayFile($filePhp);
    }

    public function testConstructWithEmptyFilePhp(): void
    {
        $this->file->create();
        $filePhp = new FilePhp($this->file);
        $this->expectException(FileWithoutContentsException::class);
        new ArrayFile($filePhp);
    }

    public function testConstructWithNoArrayType(): void
    {
        $this->file->create();
        $this->file->put("<?php return 'test'; ");
        $filePhp = new FilePhp($this->file);
        $this->expectException(FileReturnInvalidTypeException::class);
        new ArrayFile($filePhp);
    }

    public function testConstruct(): void
    {
        $this->file->create();
        $array = ['test'];
        $this->file->put('<?php return ' . var_export($array, true) . ';');
        $filePhp = new FilePhp($this->file);
        $arrayFile = new ArrayFile($filePhp);
        $this->file->remove();
        $this->assertSame($array, $arrayFile->array());
        $this->assertSame($this->file, $arrayFile->file());
    }

    public function testWithMembersTypeFail(): void
    {
        $this->file->create();
        $array = ['string', 1, 1.1];
        $this->file->put('<?php return ' . var_export($array, true) . ';');
        $filePhp = new FilePhp($this->file);
        $this->expectException(ArrayFileTypeException::class);
        (new ArrayFile($filePhp))
            ->withMembersType(new Type('string'));
    }

    public function testWithMembersType(): void
    {
        $this->file->create();
        $array = [0, 1, 2, 3];
        $this->file->put('<?php return ' . var_export($array, true) . ';');
        $filePhp = new FilePhp($this->file);
        $this->expectNotToPerformAssertions();
        (new ArrayFile($filePhp))
            ->withMembersType(new Type('integer'));
    }

    public function testWithMembersTypeObject(): void
    {
        $this->file->create();
        $this->file->put('<?php use Chevere\Components\Path\PathApp; return [new PathApp("test"), new PathApp("test-alt")];');
        $filePhp = new FilePhp($this->file);
        $this->expectNotToPerformAssertions();
        (new ArrayFile($filePhp))
            ->withMembersType(new Type(PathContract::class));
    }
}
