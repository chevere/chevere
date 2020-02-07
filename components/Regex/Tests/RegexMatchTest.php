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

namespace Chevere\Components\Regex\Tests;

use Chevere\Components\Regex\Exceptions\RegexException;
use Chevere\Components\Route\WildcardMatch;
use PHPUnit\Framework\TestCase;

final class RegexMatchTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(RegexException::class);
        new WildcardMatch('#');
    }

    public function testConstructInvalidArgument2(): void
    {
        $this->expectException(RegexException::class);
        new WildcardMatch('te(s)t');
    }

    public function testConstruct(): void
    {
        $regexMatchString = '[a-z]+';
        $regexMath = new WildcardMatch($regexMatchString);
        $this->assertSame($regexMatchString, $regexMath->toString());
    }
}
