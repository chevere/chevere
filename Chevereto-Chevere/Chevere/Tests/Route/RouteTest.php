<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Route;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Method;
use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Regex\RegexMatch;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\Wildcard;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Route\WildcardContract;
use Chevere\TestApp\App\Controllers\TestController;
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    private function getRoute(string $path): RouteContract
    {
        return new Route(
            new PathUri($path)
        );
    }

    public function testConstruct(): void
    {
        $pathUri = new PathUri('/');
        $route = new Route($pathUri);
        $this->assertSame($pathUri, $route->pathUri());
        $this->assertSame(__FILE__, $route->maker()['file']);
        $this->assertFalse($route->hasMiddlewareNameCollection());
        $this->assertFalse($route->hasWildcardCollection());
        $this->assertFalse($route->hasName());
        $this->expectException(MethodNotFoundException::class);
        $route->controllerName(new Method('GET'));
    }

    public function testConstructWithWildcard(): void
    {
        $wildcard = new Wildcard('test');
        $route = $this->getRoute('/' . $wildcard->toString());
        $this->assertTrue($route->hasWildcardCollection());
        $this->assertTrue($route->wildcardCollection()->has($wildcard));
        $this->assertEquals($wildcard, $route->wildcardCollection()->get($wildcard));
        $this->assertStringContainsString(WildcardContract::REGEX_MATCH_DEFAULT, $route->regex());
    }

    public function testWithName(): void
    {
        $name = new RouteName('name-test');
        $route = $this->getRoute('/test')
          ->withName($name);
        $this->assertTrue($route->hasName());
        $this->assertSame($name, $route->name());
    }

    public function testWithNoApplicableWildcard(): void
    {
        $this->expectException(WildcardNotFoundException::class);
        $this->getRoute('/test')
            ->withAddedWildcard(new Wildcard('test'));
    }

    public function testWithAddedWildcards(): void
    {
        $wildcards = [
            (new Wildcard('test1'))->withRegexMatch(
                new RegexMatch('[0-9]+')
            ),
            (new Wildcard('test2'))->withRegexMatch(
                new RegexMatch('[A-Z]*')
            ),
            (new Wildcard('test3'))->withRegexMatch(
                new RegexMatch('.*')
            ),
        ];
        $path = '/test/';
        foreach ($wildcards as $wildcard) {
            $path .= $wildcard->toString();
        }
        $route = $this->getRoute($path);
        foreach ($wildcards as $wildcard) {
            $route = $route
                ->withAddedWildcard($wildcard);
        }
        $this->assertTrue($route->hasWildcardCollection());
        foreach ($wildcards as $wildcard) {
            $this->assertTrue($route->wildcardCollection()->has($wildcard));
            $this->assertSame($wildcard, $route->wildcardCollection()->get($wildcard));
            // $this->assertStringContainsString($wildcard->regexMatch(), $route->regex());
        }
    }

    public function testWithAddedMethod(): void
    {
        $method = new Method('GET');
        $route = $this->getRoute('/test')
            ->withAddedMethod(
                $method,
                new ControllerName(TestController::class)
            );
        $this->assertSame(TestController::class, $route->controllerName($method)->toString());
        $this->expectException(MethodNotFoundException::class);
        $route->controllerName(new Method('POST'));
    }

    public function testWithAddedMiddleware(): void
    {
        $middlewareName = new MiddlewareName(TestMiddlewareVoid::class);
        $route = $this->getRoute('/test')
            ->withAddedMiddlewareName($middlewareName);
        $this->assertTrue($route->middlewareNameCollection()->hasAny());
        $this->assertTrue($route->middlewareNameCollection()->has($middlewareName));
    }
}
