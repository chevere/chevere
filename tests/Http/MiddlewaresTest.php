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

namespace Chevere\Tests\Http;

use Chevere\Http\Middlewares;
use Chevere\Tests\Http\_resources\MiddlewareAltTest;
use Chevere\Tests\Http\_resources\MiddlewareTest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewaresTest extends TestCase
{
    public function testEmpty(): void
    {
        $middlewares = new Middlewares();
        $this->assertCount(0, $middlewares);
    }

    public function testConstruct(): void
    {
        $middleware = new MiddlewareTest();
        $middlewares = new Middlewares($middleware);
        $this->assertCount(1, $middlewares);
        $this->assertSame([0], $middlewares->keys());
        $this->assertSame(
            [$middleware],
            iterator_to_array($middlewares->getIterator())
        );
    }

    public function testWithAppend(): void
    {
        $middlewareTest = new MiddlewareTest();
        $middlewareAlt = new MiddlewareAltTest();
        $middlewares = new Middlewares($middlewareTest);
        $httpMiddlewareWith = $middlewares->withAppend($middlewareAlt);
        $this->assertNotSame($middlewares, $httpMiddlewareWith);
        $this->assertCount(1, $middlewares);
        $this->assertCount(2, $httpMiddlewareWith);
        $this->assertSame([0, 1], $httpMiddlewareWith->keys());
        $array = array_map(function (MiddlewareInterface $middleware) {
            return $middleware::class;
        }, iterator_to_array($httpMiddlewareWith->getIterator()));
        $this->assertSame(
            [$middlewareTest::class, $middlewareAlt::class],
            $array
        );
    }

    public function testWithPrepend(): void
    {
        $middlewareTest = new MiddlewareTest();
        $middlewareAlt = new MiddlewareAltTest();
        $httpMiddleware = new Middlewares($middlewareTest);
        $httpMiddlewareWith = $httpMiddleware->withPrepend($middlewareAlt);
        $this->assertNotSame($httpMiddleware, $httpMiddlewareWith);
        $this->assertCount(1, $httpMiddleware);
        $this->assertCount(2, $httpMiddlewareWith);
        $this->assertSame([0, 1], $httpMiddlewareWith->keys());
        $array = array_map(function (MiddlewareInterface $middleware) {
            return $middleware::class;
        }, iterator_to_array($httpMiddlewareWith->getIterator()));
        $this->assertSame(
            [$middlewareAlt::class, $middlewareTest::class],
            $array
        );
    }
}