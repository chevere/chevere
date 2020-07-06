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

namespace Chevere\Tests\Regex;

use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Regex\RegexException;
use Chevere\Exceptions\Regex\RegexInvalidException;
use PHPUnit\Framework\TestCase;

final class RegexTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(RegexInvalidException::class);
        new Regex('#');
    }

    public function testAssertNoCapture(): void
    {
        $this->expectException(RegexException::class);
        (new Regex('/^(.*)$/'))->assertNoCapture();
    }

    public function testConstruct(): void
    {
        $pattern = '\w+';
        $patternAnchors = "^$pattern$";
        $patternDelimitersAnchors = "/$patternAnchors/";
        $regex = new Regex($patternDelimitersAnchors);
        $this->assertSame($patternDelimitersAnchors, $regex->toString());
        $regex->assertNoCapture();
        $this->assertSame($patternAnchors, $regex->toNoDelimiters());
        $this->assertSame($pattern, $regex->toNoDelimitersNoAnchors());
    }
}
