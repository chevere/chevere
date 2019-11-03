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

namespace Chevere\Tests\App;

use Chevere\Components\App\App;
use Chevere\Components\App\Exceptions\AppWithoutRequestException;
use Chevere\Components\App\Exceptions\MiddlewareNamesEmptyException;
use Chevere\Components\App\MiddlewareRunner;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Route\MiddlewareNames;
use Chevere\TestApp\App\Middlewares\TestVoid;
use PHPUnit\Framework\TestCase;

final class MiddlewareRunnerTest extends TestCase
{
    public function testConstructorAppWithoutRequest(): void
    {
        $app = new App(new Services(), new Response());
        $this->expectException(AppWithoutRequestException::class);
        new MiddlewareRunner(new MiddlewareNames(), $app);
    }

    public function testConstructorMiddlewareNamesEmpty(): void
    {
        $app = (new App(new Services(), new Response()))
            ->withRequest(new Request('GET', '/'));
        $this->expectException(MiddlewareNamesEmptyException::class);
        new MiddlewareRunner(new MiddlewareNames(), $app);
    }

    public function testConstructor(): void
    {
        $this->expectNotToPerformAssertions();
        $app = (new App(new Services(), new Response()))
            ->withRequest(new Request('GET', '/'));
        $middlewareNames = (new MiddlewareNames())
            ->withAddedMiddlewareName(TestVoid::class);
        new MiddlewareRunner($middlewareNames, $app);
    }

    public function testWithRun(): void
    {
        $app = (new App(new Services(), new Response()))
            ->withRequest(new Request('GET', '/'));
        $middlewareNames = (new MiddlewareNames())
            ->withAddedMiddlewareName(TestVoid::class);
        $middlewareRunner = (new MiddlewareRunner($middlewareNames, $app))
            ->withRun();
        $this->assertContainsEquals(TestVoid::class, $middlewareRunner->record());
    }
}
