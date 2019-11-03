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

use LogicException;
use TypeError;

use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Exceptions\BuildFileNotExistsException;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Dir\Dir;
use Chevere\Components\File\File;
use Chevere\Components\Http\Response;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker;
use Chevere\Contracts\App\CheckoutContract;
use PHPUnit\Framework\TestCase;

final class BuildTest extends TestCase
{
    public function testConstructor(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $this->assertSame(false, $build->isMaked());
        $this->assertSame($services, $build->app()->services());
        $this->assertInstanceOf(File::class, $build->file());
        $this->assertInstanceOf(Dir::class, $build->cacheDir());
    }

    public function testWithParameters(): void
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
            ->withParameters($parameters);

        $this->assertTrue($build->hasParameters());
        $this->assertSame($parameters, $build->parameters());
    }

    public function testWithRouterMaker(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $routerMaker = new Maker();
        $build = $build
            ->withRouterMaker($routerMaker);

        $this->assertTrue($build->hasRouterMaker());
        $this->assertSame($routerMaker, $build->routerMaker());
    }

    public function testMakeWithoutRequirements(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $this->expectException(LogicException::class);
        $build->make();
    }

    public function testMakeAndDestroy(): void
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

        $this->assertEquals(true, $build->isMaked());
        $this->assertIsArray($build->checksums());
        $this->assertInstanceOf(CheckoutContract::class, $build->checkout());
        $build->destroy();
    }

    public function testInvalidDestroyMethodCall(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $this->expectException(BuildFileNotExistsException::class);
        $build->destroy();
    }

    public function testInvalidChecksumsMethodCall(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $this->expectException(TypeError::class);
        $build->checksums();
    }

    public function testInvalidCheckoutMethodCall(): void
    {
        $services = new Services();
        $response = new Response();
        $app = new App($services, $response);
        $build = new Build($app);
        $this->expectException(TypeError::class);
        $build->checkout();
    }
}
