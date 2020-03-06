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

use Chevere\Components\Route\RouteWildcardMatch;
use Chevere\Components\Route\Exceptions\RouteWildcardInvalidCharsException;
use Chevere\Components\Route\Exceptions\RouteWildcardNotFoundException;
use Chevere\Components\Route\Exceptions\RouteWildcardStartWithNumberException;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Route\RouteWildcard;
use Chevere\Components\Route\Interfaces\RouteWildcardInterface;
use PHPUnit\Framework\TestCase;

final class RouteWildcardTest extends TestCase
{
    public function testConstructWildcardStartsWithInvalidChar(): void
    {
        $this->expectException(RouteWildcardStartWithNumberException::class);
        new RouteWildcard('0test');
    }

    public function testConstructWildcardInvalidChars(): void
    {
        $this->expectException(RouteWildcardInvalidCharsException::class);
        new RouteWildcard('t{e/s}t');
    }

    public function testConstruct(): void
    {
        $name = 'test';
        $routeWildcard = new RouteWildcard($name);
        $routeWildcardMatch = new RouteWildcardMatch(RouteWildcardInterface::REGEX_MATCH_DEFAULT);
        $this->assertSame($name, $routeWildcard->name());
        $this->assertSame("{{$name}}", $routeWildcard->toString());
        $this->assertSame($routeWildcardMatch->toString(), $routeWildcard->match()->toString());
    }

    public function testWithRegex(): void
    {
        $name = 'test';
        $routeWildcardMatch = new RouteWildcardMatch('[a-z]+');
        $routeWildcard = (new RouteWildcard($name))
            ->withMatch($routeWildcardMatch);
        $this->assertSame($name, $routeWildcard->name());
        $this->assertSame($routeWildcardMatch, $routeWildcard->match());
    }

    public function testAssertPathWildcardNotExists(): void
    {
        $this->expectException(RouteWildcardNotFoundException::class);
        (new RouteWildcard('test'))
            ->assertRoutePath(
                new RoutePath('/')
            );
    }

    public function testAssertPath(): void
    {
        $this->expectNotToPerformAssertions();
        (new RouteWildcard('test'))
            ->assertRoutePath(
                new RoutePath('/{test}')
            );
    }
}
