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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Regex\Exceptions\RegexException;
use Chevere\Components\Route\RouteWildcardMatch;
use PHPUnit\Framework\TestCase;

final class RouteWildcardMatchTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(RegexException::class);
        new RouteWildcardMatch('#');
    }

    public function testConstructInvalidArgument2(): void
    {
        $this->expectException(RegexException::class);
        new RouteWildcardMatch('te(s)t');
    }

    public function testConstruct(): void
    {
        $regexMatchString = '[a-z]+';
        $regexMath = new RouteWildcardMatch($regexMatchString);
        $this->assertSame($regexMatchString, $regexMath->toString());
    }
}
