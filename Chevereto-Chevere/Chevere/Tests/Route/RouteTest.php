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
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\Wildcard;
use Chevere\Contracts\Route\WildcardContract;
use Chevere\TestApp\App\Controllers\Test;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testConstruct(): void
    {
        $pathUri = new PathUri('/');
        $route = new Route($pathUri);
        $this->assertSame($pathUri, $route->pathUri());
        $this->assertSame(__FILE__, $route->maker()['file']);
        $this->assertFalse($route->hasWildcardCollection());
        $this->assertFalse($route->hasName());
        $this->expectException(MethodNotFoundException::class);
        $route->controllerName(new Method('GET'));
    }

    public function testConstructWithWildcard(): void
    {
        $wildcard = new Wildcard('test');
        $pathUri = new PathUri('/' . $wildcard->toString());
        $route = new Route($pathUri);
        $this->assertTrue($route->hasWildcardCollection());
        $this->assertTrue($route->wildcardCollection()->has($wildcard));
        $this->assertEquals($wildcard, $route->wildcardCollection()->get($wildcard));
        $this->assertStringContainsString(WildcardContract::REGEX_MATCH_DEFAULT, $route->regex());
    }

    public function testWithName(): void
    {
        $name = new RouteName('name-test');
        $route = (new Route(new PathUri('/test')))
          ->withName($name);
        $this->assertTrue($route->hasName());
        $this->assertSame($name, $route->name());
    }

    public function testWithNoApplicableWildcard(): void
    {
        $this->expectException(WildcardNotFoundException::class);
        (new Route(new PathUri('/test')))
            ->withAddedWildcard(new Wildcard('test'));
    }

    public function testWithAddedWildcards(): void
    {
        $wildcards = [
            (new Wildcard('test1'))->withRegex('[0-9]+'),
            (new Wildcard('test2'))->withRegex('[A-Z]*'),
            (new Wildcard('test3'))->withRegex('.*'),
        ];
        $path = '/test/';
        foreach ($wildcards as $wildcard) {
            $path .= $wildcard->toString();
        }
        $route = new Route(new PathUri($path));
        foreach ($wildcards as $wildcard) {
            $route = $route
                ->withAddedWildcard($wildcard);
        }
        $this->assertTrue($route->hasWildcardCollection());
        foreach ($wildcards as $wildcard) {
            $this->assertTrue($route->wildcardCollection()->has($wildcard));
            $this->assertSame($wildcard, $route->wildcardCollection()->get($wildcard));
            $this->assertStringContainsString($wildcard->regex(), $route->regex());
        }
    }

    public function testWithAddedMethodControllerName(): void
    {
        $route = new Route(new PathUri('/test'));
        $method = new Method('GET');
        $route = $route
            ->withAddedMethodControllerName(
                new MethodControllerName(
                    $method,
                    new ControllerName(Test::class)
                )
        );
        $this->assertSame(Test::class, $route->controllerName($method)->toString());
        $this->expectException(MethodNotFoundException::class);
        $route->controllerName(new Method('POST'));
    }

    public function testWithAddedMiddleware(): void
    {
    }
}
