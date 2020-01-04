<?php

namespace Chevere\Tests\App;

use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Builder;
use Chevere\Components\App\Resolvable;
use Chevere\Components\App\Resolver;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterProperties;
use Chevere\Contracts\App\ResolvableContract;
use PHPUnit\Framework\TestCase;

final class ResolverTest extends TestCase
{
    private function getResolvable(): ResolvableContract
    {
        $properties = (new RouterProperties())
            ->withRegex('#^(.*)$ (*:0)#');
        $router = (new Router())
            ->withProperties($properties);
        $services = (new Services())
            ->withRouter($router);
        $app = new App($services, new Response());
        $app = $app->withRequest(
            new Request('GET', '/resolver')
        );
        return
            new Resolvable(
                new Builder(
                    new Build($app)
                )
            );
    }
    public function testRouteNotFound(): void
    {
        $resolvable = $this->getResolvable();
        $resolver = new Resolver($resolvable);
    }

    // public function testMethodNotFound(): void
    // {
    // }
}
