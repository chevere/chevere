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

use Chevere\Components\Api\Api;
use Chevere\Components\App\Build;
use Chevere\Components\App\Exceptions\BuildFileNotExistsException;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Dir\Dir;
use Chevere\Components\File\File;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\CheckoutContract;
use PHPUnit\Framework\TestCase;

final class BuildTest extends TestCase
{
    public function testConstructor(): void
    {
        $services = new Services();
        $build = new Build($services);
        $this->assertSame(false, $build->isMaked());
        $this->assertSame($services, $build->services());
        $this->assertInstanceOf(File::class, $build->file());
        $this->assertInstanceOf(Dir::class, $build->cacheDir());
    }

    public function testWithServices(): void
    {
        $build = new Build(new Services());
        $services = (new Services())
            ->withApi(new Api())
            ->withRouter(new Router());
        $build = $build
            ->withServices($services);

        $this->assertSame($services, $build->services());
    }

    public function testWithParameters(): void
    {
        $build = new Build(new Services());
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
        $build = new Build(new Services());
        $routerMaker = new Maker();
        $build = $build
            ->withRouterMaker($routerMaker);

        $this->assertTrue($build->hasRouterMaker());
        $this->assertSame($routerMaker, $build->routerMaker());
    }

    public function testMakeWithoutRequirements(): void
    {
        $build = new Build(new Services());
        $this->expectException(LogicException::class);
        $build->make();
    }

    public function testMakeAndDestroy(): void
    {
        $build = new Build(new Services());
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
        $build = new Build($services);
        $this->expectException(BuildFileNotExistsException::class);
        $build->destroy();
    }

    public function testInvalidChecksumsMethodCall(): void
    {
        $services = new Services();
        $build = new Build($services);
        $this->expectException(TypeError::class);
        $build->checksums();
    }

    public function testInvalidCheckoutMethodCall(): void
    {
        $services = new Services();
        $build = new Build($services);
        $this->expectException(TypeError::class);
        $build->checkout();
    }
}
