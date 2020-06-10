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

namespace Chevere\Tests\Spec;

use Chevere\Interfaces\Spec\SpecPathInterface;
use Chevere\Components\Spec\SpecPath;
use Chevere\Exceptions\Str\StrContainsException;
use Chevere\Exceptions\Str\StrEmptyException;
use Chevere\Exceptions\Str\StrEndsWithException;
use Chevere\Exceptions\Str\StrNotStartsWithException;
use Chevere\Exceptions\Str\StrStartsWithException;
use PHPUnit\Framework\TestCase;

final class SpecPathTest extends TestCase
{
    private SpecPathInterface $specPath;

    public function setUp(): void
    {
        $this->specPath = new SpecPath('/spec');
    }

    public function testPubEmpty(): void
    {
        $this->expectException(StrEmptyException::class);
        new SpecPath('');
    }

    public function testPubSpace(): void
    {
        $this->expectException(StrContainsException::class);
        new SpecPath(' ');
    }

    public function testPubInvalidFirstChar(): void
    {
        $this->expectException(StrNotStartsWithException::class);
        new SpecPath('spec');
    }

    public function testPubDoubleForwardSlashes(): void
    {
        $this->expectException(StrContainsException::class);
        new SpecPath('/sp//ec');
    }

    public function testPubBackwardSlashes(): void
    {
        $this->expectException(StrContainsException::class);
        new SpecPath('/sp\ec');
    }

    public function testPubEndsWithSlash(): void
    {
        $this->expectException(StrEndsWithException::class);
        new SpecPath('/spec/');
    }

    public function testConstruct(): void
    {
        $pub = '/spec';
        $specPath = new SpecPath($pub);
        $this->assertSame($pub, $specPath->pub());
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
        $child = 'child';
        $specPath = new SpecPath($pub);
        $getChild = $specPath->getChild($child);
        $this->assertSame($pub . '/' . $child, $getChild->pub());
        $this->assertSame($child, basename($getChild->pub()));
    }
}
