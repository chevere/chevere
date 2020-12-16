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

namespace Chevere\Tests\Router\Route;

use Chevere\Components\Router\Route\RouteWildcard;
use Chevere\Components\Router\Route\RouteWildcardMatch;
use Chevere\Exceptions\Router\Route\RouteWildcardInvalidException;
use FastRoute\RouteParser\Std;
use PHPUnit\Framework\TestCase;

final class RouteWildcardTest extends TestCase
{
    public function testConstructWildcardStartsWithInvalidChar(): void
    {
        $this->expectException(RouteWildcardInvalidException::class);
        new RouteWildcard('0test', new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX));
    }

    public function testConstructWildcardInvalidChars(): void
    {
        $this->expectException(RouteWildcardInvalidException::class);
        new RouteWildcard('t{e/s}t', new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX));
    }

    public function testWithRegex(): void
    {
        $name = 'test';
        $match = new RouteWildcardMatch('[a-z]+');
        $routeWildcard = new RouteWildcard($name, $match);
        $this->assertSame($name, $routeWildcard->toString());
        $this->assertSame($match, $routeWildcard->match());
    }
}
