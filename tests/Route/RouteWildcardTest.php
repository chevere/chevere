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

namespace Chevere\Tests\Route;

use Chevere\Components\Route\RouteWildcard;
use Chevere\Components\Route\RouteWildcardMatch;
use Chevere\Exceptions\Route\RouteWildcardInvalidCharsException;
use Chevere\Exceptions\Route\RouteWildcardStartWithNumberException;
use FastRoute\RouteParser\Std;
use PHPUnit\Framework\TestCase;

final class RouteWildcardTest extends TestCase
{
    public function testConstructWildcardStartsWithInvalidChar(): void
    {
        $this->expectException(RouteWildcardStartWithNumberException::class);
        new RouteWildcard('0test', new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX));
    }

    public function testConstructWildcardInvalidChars(): void
    {
        $this->expectException(RouteWildcardInvalidCharsException::class);
        new RouteWildcard('t{e/s}t', new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX));
    }

    public function testWithRegex(): void
    {
        $name = 'test';
        $match = new RouteWildcardMatch('[a-z]+');
        $routeWildcard = new RouteWildcard($name, $match);
        $this->assertSame($name, $routeWildcard->name());
        $this->assertSame($match, $routeWildcard->match());
    }
}
