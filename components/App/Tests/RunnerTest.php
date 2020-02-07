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
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Runner;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\Router;
use Chevere\Components\App\Interfaces\BuildInterface;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Router\RouteCache;
use Chevere\Components\Router\RouterCache;
use PHPUnit\Framework\TestCase;

final class RunnerTest extends TestCase
{
    private function getTestBuild(): BuildInterface
    {
        $build = $this->getDummyBuild();
        $parameters = new Parameters(
            new ArrayFile(
                new PhpFile(
                    new File(
                        new AppPath('parameters.php')
                    )
                )
            )
        );

        return $build
            ->withParameters($parameters);
    }

    private function getDummyBuild(): BuildInterface
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);

        return new Build($app);
    }

    public function testConstructor(): void
    {
        $build = $this->getDummyBuild();
        $builder = new Builder($build);
        $runner = new Runner($builder);
        $this->assertSame($builder, $runner->builder());
    }

    public function testWithConsoleLoop(): void
    {
        $build = $this->getDummyBuild();
        $builder = new Builder($build);
        $runner = (new Runner($builder))
            ->withConsoleLoop();
        $this->assertTrue($runner->hasConsoleLoop());
    }

    public function testWithRunnerWithoutRouter(): void
    {
        $build = $this->getDummyBuild();
        $builder = new Builder($build);
        $runner = new Runner($builder);
        $this->expectException(RouterRequiredException::class);
        $runner->withRun();
    }

    public function testWithRunWithRouterUnableToResolve(): void
    {
        $routeCache = new RouteCache(new Cache(new Dir(new Path(__DIR__))));
        $router = new Router($routeCache);
        $services = (new Services())
            ->withRouter($router);
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $builder = new Builder($build);
        $runner = new Runner($builder);
        $this->expectException(RouterCantResolveException::class);
        $runner->withRun();
    }

    // public function testRunnerNotFound(): void
    // {
    //     $build = $this->getTestBuild();
    //     $builder = new Builder($build->make());
    //     $app = $builder->build()->app()
    //         ->withRequest(
    //             new Request(
    //                 new Method('GET'),
    //                 new PathUri('/404')
    //             )
    //         );
    //     $builder = $builder
    //         ->withBuild(
    //             $builder->build()->withApp($app)
    //         );
    //     $runner = new Runner($builder);
    //     $ranBuilder = $runner->withRun()->builder();
    //     $this->assertSame(404, $ranBuilder->build()->app()->response()->guzzle()->getStatusCode());
    //     $build->destroy();
    // }

    // public function testRunnerFoundBadMethod(): void
    // {
    //     $build = $this->getTestBuild();
    //     $builder = new Builder($build->make());
    //     $app = $builder->build()->app()
    //         ->withRequest(
    //             new Request(
    //                 new Method('POST'),
    //                 new PathUri('/test')
    //             )
    //         );
    //     $builder = $builder
    //         ->withBuild(
    //             $builder->build()->withApp($app)
    //         );
    //     $runner = new Runner($builder);
    //     $ranBuilder = $runner->withRun()->builder();
    //     $build->destroy();
    //     $this->assertSame(405, $ranBuilder->build()->app()->response()->guzzle()->getStatusCode());
    // }

    // public function testRunnerFound(): void
    // {
    //     $build = $this->getTestBuild();
    //     $builder = new Builder($build->make());
    //     $app = $builder->build()->app()
    //         ->withRequest(
    //             new Request(
    //                 new Method('GET'),
    //                 new PathUri('/test')
    //             )
    //         );
    //     $builder = $builder
    //         ->withBuild(
    //             $builder->build()->withApp($app)
    //         );
    //     $runner = new Runner($builder);
    //     $ranBuilder = $runner->withRun()->builder();
    //     $build->destroy();
    //     $this->assertSame(200, $ranBuilder->build()->app()->response()->guzzle()->getStatusCode());
    // }
}
