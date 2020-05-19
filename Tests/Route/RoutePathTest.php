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

use BadMethodCallException;
use Chevere\Interfaces\Regex\RegexInterface;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Exceptions\RoutePathForwardSlashException;
use Chevere\Components\Route\Exceptions\RoutePathInvalidCharsException;
use Chevere\Components\Route\Exceptions\RoutePathUnmatchedBracesException;
use Chevere\Components\Route\Exceptions\RoutePathUnmatchedWildcardsException;
use Chevere\Components\Route\Exceptions\RouteWildcardNotFoundException;
use Chevere\Components\Route\Exceptions\RouteWildcardRepeatException;
use Chevere\Components\Route\Exceptions\RouteWildcardReservedException;
use Chevere\Interfaces\Route\RoutePathInterface;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Route\RouteWildcard;
use Chevere\Components\Route\RouteWildcardMatch;
use PHPUnit\Framework\TestCase;

final class RoutePathTest extends TestCase
{
    public function testConstructNoForwardSlash(): void
    {
        $this->expectException(RoutePathForwardSlashException::class);
        new RoutePath('test');
    }

    public function testConstructIllegalChars(): void
    {
        $this->expectException(RoutePathInvalidCharsException::class);
        new RoutePath('//{{\\}} ');
    }

    public function testConstructNotMatchingBraces(): void
    {
        $this->expectException(RoutePathUnmatchedBracesException::class);
        new RoutePath('/test/{test/}/}/test');
    }

    public function testConstructWithInvalidWildcard(): void
    {
        $this->expectException(RoutePathUnmatchedWildcardsException::class);
        new RoutePath('/{wild-card}');
    }

    public function testConstructWithWildcardReserved(): void
    {
        $this->expectException(RouteWildcardReservedException::class);
        new RoutePath('/{0}');
    }

    public function testConstructWithWildcardTwiceSame(): void
    {
        $this->expectException(RouteWildcardRepeatException::class);
        new RoutePath('/test/{wildcard}/{wildcard}');
    }

    public function testConstruct(): void
    {
        $path = '/test';
        $regex = $this->wrapRegex('^' . $path . '$');
        $routePath = new RoutePath($path);
        $this->assertSame($path, $routePath->toString());
        $this->assertSame($path, $routePath->key());
        $this->assertEquals($regex, $routePath->regex());
        $this->assertFalse($routePath->wildcards()->hasAny());
        $this->expectException(BadMethodCallException::class);
        $routePath->uriFor([]);
    }

    public function testConstructWithWildcard(): void
    {
        $routeWildcard = new RouteWildcard('wildcard');
        $path = '/test/' . $routeWildcard->toString() . '/test';
        $key = '/test/{0}/test';
        $regex = $this->wrapRegex('^' . str_replace('{0}', '(' . $routeWildcard->match()->toString() . ')', $key) . '$');
        $routePath = new RoutePath($path);
        $this->assertSame($path, $routePath->toString());
        $this->assertSame($key, $routePath->key());
        $this->assertEquals($regex, $routePath->regex());
        $this->assertTrue($routePath->wildcards()->hasAny());
        $this->assertTrue($routePath->wildcards()->has($routeWildcard));
    }

    public function testConstructWithWildcards(): void
    {
        $routeWildcard1 = new RouteWildcard('wildcard1');
        $routeWildcard2 = new RouteWildcard('wildcard2');
        $path = '/test/' . $routeWildcard1->toString() . '/test/' . $routeWildcard2->toString();
        $key = '/test/{0}/test/{1}';
        $regex = $this->wrapRegex('^' . strtr($key, [
            '{0}' => '(' . $routeWildcard1->match()->toString() . ')',
            '{1}' => '(' . $routeWildcard2->match()->toString() . ')',
        ]) . '$');
        $routePath = new RoutePath($path);
        $this->assertSame($path, $routePath->toString());
        $this->assertSame($key, $routePath->key());
        $this->assertEquals($regex, $routePath->regex());
        $this->assertTrue($routePath->wildcards()->hasAny());
        $this->assertTrue($routePath->wildcards()->has($routeWildcard1));
        $this->assertTrue($routePath->wildcards()->has($routeWildcard2));
    }

    public function testWithNoApplicableWildcard(): void
    {
        $this->expectException(RouteWildcardNotFoundException::class);
        (new RoutePath('/test'))
            ->withWildcard(new RouteWildcard('wildcard'));
    }

    public function testRegex(): void
    {
        $match = '[a-z]+';
        $path = '/test/{id}';
        $regex = $this->wrapRegex('^' . str_replace('{id}', "($match)", $path) . '$');
        $routePath = (new RoutePath($path))
            ->withWildcard(
                (new RouteWildcard('id'))
                    ->withMatch(new RouteWildcardMatch($match))
            );
        $this->assertEquals($regex, $routePath->regex());
    }

    public function testUriFor(): void
    {
        $id = 123;
        $wildcard = 'abc';
        $path = '/test/{id}/some/{wildcard}';
        $routePath = new RoutePath($path);
        $this->assertSame(
            strtr($path, [
                '{id}' => $id,
                '{wildcard}' => $wildcard,
            ]),
            $routePath->uriFor([
                'id' => 123,
                'wildcard' => 'abc'
            ])
        );
        $this->expectException(RoutePathUnmatchedBracesException::class);
        $routePath->uriFor([]);
    }

    private function wrapRegex(string $pattern): RegexInterface
    {
        return new Regex(
            RoutePathInterface::REGEX_DELIMITER_CHAR . $this->escapeRegex($pattern) . RoutePathInterface::REGEX_DELIMITER_CHAR
        );
    }

    private function escapeRegex(string $pattern): string
    {
        return str_replace('/', '\/', $pattern);
    }
}
