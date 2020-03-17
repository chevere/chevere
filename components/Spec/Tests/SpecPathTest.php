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

namespace Chevere\Components\Spec\Tests;

use Chevere\Components\Filesystem\Path;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Str\Exceptions\StrContainsException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use Chevere\Components\Str\Exceptions\StrEndsWithException;
use Chevere\Components\Str\Exceptions\StrNotStartsWithException;
use Chevere\Components\Str\Exceptions\StrStartsWithException;
use PHPUnit\Framework\TestCase;

final class SpecPathTest extends TestCase
{
    private SpecPathInterface $specPath;

    public function setUp(): void
    {
        $this->specPath = new SpecPath('/spec', new Path(__DIR__));
    }

    public function testPubEmpty(): void
    {
        $this->expectException(StrEmptyException::class);
        new SpecPath('', new Path(__DIR__));
    }

    public function testPubSpace(): void
    {
        $this->expectException(StrContainsException::class);
        new SpecPath(' ', new Path(__DIR__));
    }

    public function testPubInvalidFirstChar(): void
    {
        $this->expectException(StrNotStartsWithException::class);
        new SpecPath('spec', new Path(__DIR__));
    }

    public function testPubDoubleForwardSlashes(): void
    {
        $this->expectException(StrContainsException::class);
        new SpecPath('/sp//ec', new Path(__DIR__));
    }

    public function testPubBackwardSlashes(): void
    {
        $this->expectException(StrContainsException::class);
        new SpecPath('/sp\ec', new Path(__DIR__));
    }

    public function testPubEndswithSlash(): void
    {
        $this->expectException(StrEndsWithException::class);
        new SpecPath('/spec/', new Path(__DIR__));
    }

    public function testConstruct(): void
    {
        $pub = '/spec';
        $path = new Path(__DIR__);
        $specPath = new SpecPath($pub, $path);
        $this->assertSame($pub, $specPath->pub());
        $this->assertSame($path, $specPath->path());
    }

    public function testGetChildEmpty(): void
    {
        $this->expectException(StrEmptyException::class);
        $this->specPath->getChild('');
    }

    public function testGetChildSpaces(): void
    {
        $this->expectException(StrContainsException::class);
        $this->specPath->getChild(' ');
    }

    public function testGetChildDoubleForwardSlashes(): void
    {
        $this->expectException(StrContainsException::class);
        $this->specPath->getChild('chi//ld');
    }

    public function testGetChildBackwardSlashes(): void
    {
        $this->expectException(StrContainsException::class);
        $this->specPath->getChild('chi\ld');
    }

    public function testGetChildStartsWithForwardSlash(): void
    {
        $this->expectException(StrStartsWithException::class);
        $this->specPath->getChild('/child');
    }

    public function testGetChildEndsWithForwardSlash(): void
    {
        $this->expectException(StrEndsWithException::class);
        $this->specPath->getChild('child/');
    }

    public function testGetChild(): void
    {
        $pub = '/spec';
        $path = new Path(__DIR__);
        $child = 'chiquillo';
        $specPath = new SpecPath($pub, $path);
        $getChild = $specPath->getChild($child);
        $this->assertSame($pub . '/' . $child, $getChild->pub());
        $this->assertSame($child, basename($getChild->path()->absolute()));
    }
}
