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
use Chevere\Components\App\Services;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Routed;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    public function testConstructor(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);

        $this->assertSame($response, $app->response());
        $this->assertSame($services, $app->services());
        $this->assertFalse($app->hasRequest());
        $this->assertFalse($app->hasRouted());
        $this->assertFalse($app->hasArguments());
    }

    public function testWithResponse(): void
    {
        $app = new App(new Services(), new Response());
        $response = new Response();
        $response = $response
            ->withGuzzle(
                $response->guzzle()
                    ->withHeader('header', 'test')
            );
        $app = $app
            ->withResponse($response);
        $this->assertSame($response, $app->response());
    }

    public function testWithRequest(): void
    {
        $app = new App(new Services(), new Response());
        $request =
            new Request(
                new Method('GET'),
                new PathUri('/')
            );
        $app = $app
            ->withRequest($request);
        $this->assertTrue($app->hasRequest());
        $this->assertSame($request, $app->request());
    }

    public function testWithRouted(): void
    {
        $route = new Route(new PathUri('/home'));
        $routed = new Routed($route, []);
        $app = (new App(new Services(), new Response()))
            ->withRouted($routed);

        $this->assertTrue($app->hasRouted());
        $this->assertSame($routed, $app->routed());
    }

    public function testWithServices(): void
    {
        $services = new Services();
        $app = (new App(new Services(), new Response()))
            ->withServices($services);
        $this->assertSame($services, $app->services());
    }

    public function testWithArguments(): void
    {
        $arguments = ['a', 'b', 'c'];
        $app = (new App(new Services(), new Response()))
            ->withArguments($arguments);
        $this->assertTrue($app->hasArguments());
        $this->assertSame($arguments, $app->arguments());
    }
}
