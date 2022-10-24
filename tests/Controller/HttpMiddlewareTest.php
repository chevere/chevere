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

namespace Chevere\Tests\Controller;

use Chevere\Controller\HttpMiddleware;
use Chevere\Tests\Controller\_resources\MiddlewareAltTest;
use Chevere\Tests\Controller\_resources\MiddlewareTest;
use PHPUnit\Framework\TestCase;

final class HttpMiddlewareTest extends TestCase
{
    public function testEmpty(): void
    {
        $httpMiddleware = new HttpMiddleware();
        $this->assertCount(0, $httpMiddleware);
    }

    public function testConstruct(): void
    {
        $middleware = new MiddlewareTest();
        $httpMiddleware = new HttpMiddleware($middleware);
        $this->assertCount(1, $httpMiddleware);
        $this->assertSame([0], $httpMiddleware->keys());
        $this->assertSame(
            [$middleware],
            iterator_to_array($httpMiddleware->getIterator())
        );
    }

    public function testWithAppend(): void
    {
        $middlewareTest = new MiddlewareTest();
        $middlewareAlt = new MiddlewareAltTest();
        $httpMiddleware = new HttpMiddleware($middlewareTest);
        $httpMiddlewareWith = $httpMiddleware->withAppend($middlewareAlt);
        $this->assertNotSame($httpMiddleware, $httpMiddlewareWith);
        $this->assertNotEquals($httpMiddleware, $httpMiddlewareWith);
        $this->assertCount(2, $httpMiddlewareWith);
        $this->assertSame([0, 1], $httpMiddlewareWith->keys());
        $this->assertSame(
            [$middlewareTest, $middlewareAlt],
            iterator_to_array($httpMiddlewareWith->getIterator())
        );
    }

    public function testWithPrepend(): void
    {
        $middlewareTest = new MiddlewareTest();
        $middlewareAlt = new MiddlewareAltTest();
        $httpMiddleware = new HttpMiddleware($middlewareTest);
        $httpMiddlewareWith = $httpMiddleware->withPrepend($middlewareAlt);
        $this->assertNotSame($httpMiddleware, $httpMiddlewareWith);
        $this->assertNotEquals($httpMiddleware, $httpMiddlewareWith);
        $this->assertCount(2, $httpMiddlewareWith);
        $this->assertSame([0, 1], $httpMiddlewareWith->keys());
        $this->assertSame(
            [$middlewareAlt, $middlewareTest],
            iterator_to_array($httpMiddlewareWith->getIterator())
        );
    }
}
