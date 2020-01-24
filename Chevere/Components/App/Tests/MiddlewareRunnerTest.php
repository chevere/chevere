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

namespace Chevere\Components\App\Tests;

use Chevere\Components\App\App;
use Chevere\Components\App\Exceptions\AppWithoutRequestException;
use Chevere\Components\Middleware\Exceptions\MiddlewareNamesEmptyException;
use Chevere\Components\App\MiddlewareRunner;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Components\Route\PathUri;
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use PHPUnit\Framework\TestCase;

final class MiddlewareRunnerTest extends TestCase
{
    private function getRequest(): RequestInterface
    {
        return
            new Request(
                new Method('GET'),
                new PathUri('/')
            );
    }

    public function testConstructorAppWithoutRequest(): void
    {
        $app = new App(new Services(), new Response());
        $this->expectException(AppWithoutRequestException::class);
        new MiddlewareRunner(new MiddlewareNameCollection(), $app);
    }

    public function testConstructorMiddlewareNamesEmpty(): void
    {
        $app = (new App(new Services(), new Response()))
            ->withRequest(
                $this->getRequest()
            );
        $this->expectException(MiddlewareNamesEmptyException::class);
        new MiddlewareRunner(new MiddlewareNameCollection(), $app);
    }

    public function testConstructor(): void
    {
        $this->expectNotToPerformAssertions();
        $app = (new App(new Services(), new Response()))
            ->withRequest(
                $this->getRequest()
            );
        $middlewareNameCollection = (new MiddlewareNameCollection())
            ->withAddedMiddlewareName(
                new MiddlewareName(TestMiddlewareVoid::class)
            );
        new MiddlewareRunner($middlewareNameCollection, $app);
    }

    public function testWithRun(): void
    {
        $app = (new App(new Services(), new Response()))
            ->withRequest(
                $this->getRequest()
            );
        $middlewareNameCollection = new MiddlewareNameCollection(
            new MiddlewareName(TestMiddlewareVoid::class)
        );
        $middlewareRunner = new MiddlewareRunner($middlewareNameCollection, $app);
        $middlewareRunner = $middlewareRunner
            ->withRun();
        $this->assertContainsEquals(TestMiddlewareVoid::class, $middlewareRunner->record());
    }
}
