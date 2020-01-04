<?php

namespace Chevere\Tests\App;

use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Builder;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Resolvable;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Response;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterProperties;
use PHPUnit\Framework\TestCase;

final class ResolvableTest extends TestCase
{
    public function testConstructMissingRouter(): void
    {
        $this->expectException(RouterRequiredException::class);
        new Resolvable(
            new Builder(
                new Build(
                    new App(
                        new Services(),
                        new Response()
                    )
                )
            )
        );
    }

    public function testConstructRouterCantResolve(): void
    {
        $this->expectException(RouterCantResolveException::class);
        $services = (new Services())
            ->withRouter(new Router());
        new Resolvable(
            new Builder(
                new Build(
                    new App(
                        $services,
                        new Response()
                    )
                )
            )
        );
    }

    public function testConstructor(): void
    {
        $properties = (new RouterProperties())
            ->withRegex('*');
        $router = (new Router())
            ->withProperties($properties);
        $services = (new Services())
            ->withRouter($router);
        $this->expectNotToPerformAssertions();
        new Resolvable(
            new Builder(
                new Build(
                    new App(
                        $services,
                        new Response()
                    )
                )
            )
        );
    }
}
