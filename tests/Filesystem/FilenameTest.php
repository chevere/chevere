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

use Chevere\Components\Filesystem\Filename;
use function Chevere\Components\Str\randomString;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LengthException;
use PHPUnit\Framework\TestCase;

final class FilenameTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Filename(' ');
    }

    public function testInvalidLength(): void
    {
        $this->expectException(LengthException::class);
        new Filename(randomString(256));
    }

    public function testWithExtension(): void
    {
        $name = 'test';
        $extension = 'JPEG';
        $filename = "${name}.${extension}";
        $basename = new Filename($filename);
        $this->assertSame($filename, $basename->toString());
        $this->assertSame($extension, $basename->extension());
        $this->assertSame($name, $basename->name());
    }

    public function testWithoutExtension(): void
    {
        $name = 'test';
        $extension = '';
        $filename = $name;
        $basename = new Filename($filename);
        $this->assertSame($filename, $basename->toString());
        $this->assertSame($extension, $basename->extension());
        $this->assertSame($name, $basename->name());
    }

    public function testWithoutName(): void
    {
        $name = '';
        $extension = 'png';
        $filename = $filename = "${name}.${extension}";
        $basename = new Filename($filename);
        $this->assertSame($filename, $basename->toString());
        $this->assertSame($extension, $basename->extension());
        $this->assertSame($name, $basename->name());
    }
}
