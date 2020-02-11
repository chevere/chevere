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

namespace Chevere\Components\ArrayFile\Tests;

use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\ArrayFile\Exceptions\ArrayFileTypeException;
use Chevere\Components\Filesystem\Exceptions\File\FileNotFoundException;
use Chevere\Components\Filesystem\Exceptions\File\FileReturnInvalidTypeException;
use Chevere\Components\Filesystem\Exceptions\File\FileWithoutContentsException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Type\Type;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Throwable;

final class ArrayFileTest extends TestCase
{
    /** @var FileInterface */
    private $file;

    public function setUp(): void
    {
        $this->file = new File(
            new AppPath('var/ArrayFileTest_' . uniqid() . '.php')
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
        $filePhp = new PhpFile($this->file);
        $this->expectException(FileNotFoundException::class);
        new ArrayFile($filePhp);
    }

    public function testConstructWithEmptyFilePhp(): void
    {
        $this->file->create();
        $filePhp = new PhpFile($this->file);
        $this->expectException(FileWithoutContentsException::class);
        new ArrayFile($filePhp);
    }

    public function testConstructWithNoArrayType(): void
    {
        $this->file->create();
        $this->file->put("<?php return 'test'; ");
        $filePhp = new PhpFile($this->file);
        $this->expectException(FileReturnInvalidTypeException::class);
        new ArrayFile($filePhp);
    }

    public function testConstruct(): void
    {
        $this->file->create();
        $array = ['test'];
        $this->file->put('<?php return ' . var_export($array, true) . ';');
        $filePhp = new PhpFile($this->file);
        $arrayFile = new ArrayFile($filePhp);
        $this->file->remove();
        $this->assertSame($array, $arrayFile->array());
        $this->assertSame($this->file, $arrayFile->file());
    }

    public function testWithMembersTypeFail(): void
    {
        $this->file->create();
        $array = ['string', new stdClass, 1.1];
        $this->file->put('<?php return ' . var_export($array, true) . ';');
        $filePhp = new PhpFile($this->file);
        $this->expectException(ArrayFileTypeException::class);
        (new ArrayFile($filePhp))
            ->withMembersType(new Type('string'));
    }

    public function testWithMembersType(): void
    {
        $this->file->create();
        $array = [0, 1, 2, 3];
        $this->file->put('<?php return ' . var_export($array, true) . ';');
        $filePhp = new PhpFile($this->file);
        $this->expectNotToPerformAssertions();
        (new ArrayFile($filePhp))
            ->withMembersType(new Type('integer'));
    }

    public function testWithMembersTypeObject(): void
    {
        $this->file->create();
        $className = AppPath::class;
        $this->file->put('<?php use ' . $className . '; return [new AppPath("test"), new AppPath("test-alt")];');
        $filePhp = new PhpFile($this->file);
        $this->expectNotToPerformAssertions();
        (new ArrayFile($filePhp))
            ->withMembersType(new Type(PathInterface::class));
    }
}
