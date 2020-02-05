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
use Chevere\Components\App\Interfaces\AppInterface;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\PathUri;
use PHPUnit\Framework\TestCase;

final class ResolvableTest extends TestCase
{
    private function getApp(): AppInterface
    {
        return
            new App(
                new Services(),
                new Response()
            );
    }

    private function getAppWithRequest(): AppInterface
    {
        return
            $this->getApp()
                ->withRequest(
                    new Request(
                        new Method('GET'),
                        new PathUri('/resolvable')
                    )
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

    // public function testConstructRouterCantResolve(): void
    // {
    //     $this->expectException(RouterCantResolveException::class);
    //     new Resolvable(
    //         new Builder(
    //             new Build(
    //                 $this
    //                     ->getAppWithRequest()
    //                     ->withServices(
    //                         (new Services())
    //                             ->withRouter(
    //                                 new Router()
    //                             )
    //                     )
    //             )
    //         )
    //     );
    // }

    // public function testConstructor(): void
    // {
    //     $properties = (new RouterProperties())
    //         ->withRegex('*');
    //     $router = (new Router())
    //         ->withProperties($properties)
    //         ->withRegex()
    //         ->withIndex()
    //         ->withNamed()
    //         ->withGroups();
    //     $services = (new Services())
    //         ->withRouter($router);
    //     $this->expectNotToPerformAssertions();
    //     new Resolvable(
    //         new Builder(
    //             new Build(
    //                 $this
    //                     ->getAppWithRequest()
    //                     ->withServices($services)
    //             )
    //         )
    //     );
    // }
}
