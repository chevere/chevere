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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterProperties;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testConstructor(): void
    {
        $router = new Router();
        $this->assertFalse($router->canResolve());
        $this->assertFalse($router->hasProperties());
    }

    public function testResolveException(): void
    {
        $router = new Router();
        $this->expectException(RouterException::class);
        $router->resolve(new Uri('/'));
    }

    public function testWithProperties(): void
    {
        $properties = (new RouterProperties())
            ->withIndex(['index']);
        $router = (new Router())
            ->withProperties($properties);
        $this->assertTrue($router->hasProperties());
        $this->assertSame($properties, $router->properties());
        $this->assertFalse($router->canResolve());
    }

    // public function testResolveNotFound(): void
    // {
    //     $properties = (new RouterProperties())
    //         ->withRegex('/./')
    //         ->withIndex(['index']);
    //     $router = (new Router())
    //         ->withProperties($properties);
    //     $router->resolve(new Uri('/'));
    // }
}
