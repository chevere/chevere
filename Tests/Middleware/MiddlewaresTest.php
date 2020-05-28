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

namespace Chevere\Tests\Middleware;

use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Middleware\Middlewares;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewaresTest extends TestCase
{
    public function testConstruct(): void
    {
        $middleware = new MiddlewaresTestMiddleware;
        $middlewares = new Middlewares;
        $this->assertCount(0, $middlewares);
    }

    public function testWithAddedMiddleware(): void
    {
        $middleware = new MiddlewaresTestMiddleware;
        $middlewares = (new Middlewares)->withAddedMiddleware($middleware);
        $this->assertCount(1, $middlewares);
        $this->assertTrue($middlewares->has($middleware));
        $this->expectException(OverflowException::class);
        $middlewares = $middlewares->withAddedMiddleware($middleware);
    }
}

final class MiddlewaresTestMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response('OK', 200, []);
    }
}
