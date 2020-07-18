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

use Chevere\Components\Spec\SpecPath;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Str\StrEndsWithException;
use Chevere\Exceptions\Str\StrNotStartsWithException;
use Chevere\Interfaces\Spec\SpecPathInterface;
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
        $this->expectException(InvalidArgumentException::class);
        new SpecPath('');
    }

    public function testPubSpace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SpecPath(' ');
    }

    public function testPubInvalidFirstChar(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SpecPath('spec');
    }

    public function testPubDoubleForwardSlashes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SpecPath('/sp//ec');
    }

    public function testPubBackwardSlashes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SpecPath('/sp\ec');
    }

    public function testPubEndsWithSlash(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SpecPath('/spec/');
    }

    public function testConstruct(): void
    {
        $pub = '/spec';
        $specPath = new SpecPath($pub);
        $this->assertSame($pub, $specPath->toString());
    }

    public function testGetChildEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->specPath->getChild('');
    }

    public function testGetChildSpaces(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->specPath->getChild(' ');
    }

    public function testGetChildDoubleForwardSlashes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->specPath->getChild('chi//ld');
    }

    public function testGetChildBackwardSlashes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->specPath->getChild('chi\ld');
    }

    public function testGetChildStartsWithForwardSlash(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->specPath->getChild('/child');
    }

    public function testGetChildEndsWithForwardSlash(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->specPath->getChild('child/');
    }

    public function testGetChild(): void
    {
        $pub = '/spec';
        $child = 'child';
        $specPath = new SpecPath($pub);
        $getChild = $specPath->getChild($child);
        $this->assertSame($pub . '/' . $child, $getChild->toString());
        $this->assertSame($child, basename($getChild->toString()));
    }
}
