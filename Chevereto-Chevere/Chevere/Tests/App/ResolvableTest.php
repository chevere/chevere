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
use Chevere\Components\App\Build;
use Chevere\Components\App\Builder;
use Chevere\Components\App\Exceptions\RequestRequiredException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Resolvable;
use Chevere\Components\App\Services;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterProperties;
use Chevere\Components\App\Contracts\AppContract;
use PHPUnit\Framework\TestCase;

final class ResolvableTest extends TestCase
{
    private function getApp(): AppContract
    {
        return
            new App(
                new Services(),
                new Response()
            );
    }

    private function getAppWithRequest(): AppContract
    {
        return
            $this->getApp()
                ->withRequest(
                    new Request('GET', '/resolvable')
                );
    }

    public function testConstructMissingRequest(): void
    {
        $this->expectException(RequestRequiredException::class);
        new Resolvable(
            new Builder(
                new Build(
                    $this->getApp()
                )
            )
        );
    }

    public function testConstructMissingRouter(): void
    {
        $this->expectException(RouterRequiredException::class);
        new Resolvable(
            new Builder(
                new Build(
                    $this->getAppWithRequest()
                )
            )
        );
    }

    public function testConstructRouterCantResolve(): void
    {
        $this->expectException(RouterCantResolveException::class);
        new Resolvable(
            new Builder(
                new Build(
                    $this
                        ->getAppWithRequest()
                        ->withServices(
                            (new Services())
                                ->withRouter(
                                    new Router()
                                )
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
                    $this
                        ->getAppWithRequest()
                        ->withServices($services)
                )
            )
        );
    }
}