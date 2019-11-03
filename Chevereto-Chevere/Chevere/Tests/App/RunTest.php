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
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Run;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Http\Request;
use Chevere\Components\Http\Response;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker;
use PHPUnit\Framework\TestCase;

final class RunTest extends TestCase
{
    public function testConstructor(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $builder = new Builder($build);
        $run = new Run($builder);
        $this->assertSame($builder, $run->builder());
    }

    public function testWithConsoleLoop(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $builder = new Builder($build);
        $run = (new Run($builder))
            ->withConsoleLoop();
        $this->assertTrue($run->hasConsoleLoop());
    }

    public function testRun(): void
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
        $routerMaker = new Maker();
        $build = $build
            ->withParameters($parameters)
            ->withRouterMaker($routerMaker)
            ->make();
        
        $app = new App(new Services(), new Response());

        $builder = new Builder($build);

        $app = $builder->build()->app()
            ->withServices(
                $builder->build()->app()->services()
            );
        
        $builder = $builder
            ->withBuild(
                $builder->build()->withApp($app)
            );

        $run = new Run($builder);

        // dump($run->run());

        $build->destroy();
    }
}
