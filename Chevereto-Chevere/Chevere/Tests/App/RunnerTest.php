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
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RouterContractRequiredException;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Run;
use Chevere\Components\App\Runner;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker;
use Chevere\Components\Router\Router;
use PHPUnit\Framework\TestCase;

final class RunnerTest extends TestCase
{
    public function testConstructor(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $builder = new Builder($build);
        $runner = new Runner($builder);
        $this->assertSame($builder, $runner->builder());
    }

    public function testWithConsoleLoop(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $builder = new Builder($build);
        $runner = (new Runner($builder))
            ->withConsoleLoop();
        $this->assertTrue($runner->hasConsoleLoop());
    }

    public function testWithRunnerWithoutRouter(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $builder = new Builder($build);
        $runner = new Runner($builder);
        $this->expectException(RouterContractRequiredException::class);
        $runner->withRun();
    }
    public function testWithRunWithRouterUnableToResolve(): void
    {
        $router = new Router();
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

    public function testRunnerNotFoundWithMakeBuild(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $parameters = new Parameters(
            new ArrayFile(
                new Path('parameters.php')
            )
        );
        $build = $build
            ->withParameters($parameters)
            ->withRouterMaker(new Maker())
            ->make();

        $builder = new Builder($build);
        $app = $builder->build()->app()
            ->withRequest(new Request('GET', '/404'))
            ->withServices(
                $builder->build()->app()->services()
            );
        $builder = $builder
            ->withBuild(
                $builder->build()->withApp($app)
            );

        ob_start();
        $runner = new Runner($builder);
        $runner = $runner->withRun();
        $output = ob_get_clean();

        $build->destroy();

        dd($output, $runner->hasRouteNotFound());
    }
}
