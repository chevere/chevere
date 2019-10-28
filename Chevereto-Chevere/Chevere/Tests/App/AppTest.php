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
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Router;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase
{
    public function testConstructor(): void
    {
        $response = new Response();
        $app = new App($response);

        $this->assertSame($response, $app->response());
    }

    public function testWithResponse(): void
    {
        $app = new App(new Response());
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
        $app = new App(new Response());
        $request = new Request('GET', '/');
        $app = $app
            ->withRequest($request);
        
        $this->assertTrue($app->hasRequest());
        $this->assertSame($request, $app->request());
    }

    public function testWithRoute(): void
    {
        $route = new Route('/home');
        $app = (new App(new Response()))
            ->withRoute($route);
        
        $this->assertTrue($app->hasRoute());
        $this->assertSame($route, $app->route());
    }

    public function testWithRouter(): void
    {
        $router = new Router();
        $app = (new App(new Response()))
            ->withRouter($router);
        
        $this->assertTrue($app->hasRouter());
        $this->assertSame($router, $app->router());
    }

    public function testWithArguments(): void
    {
        $arguments = ['a', 'b', 'c'];
        $app = (new App(new Response()))
            ->withArguments($arguments);
        
        $this->assertTrue($app->hasArguments());
        $this->assertSame($arguments, $app->arguments());
    }
}
