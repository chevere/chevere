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

use TypeError;

use Chevere\Components\Api\Api;
use Chevere\Components\App\Build;
use Chevere\Components\App\Exceptions\AlreadyBuiltException;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker;
use Chevere\Components\Router\Router;
use Chevere\Components\VarDump\Dumper;
use LogicException;
use PHPUnit\Framework\TestCase;
use Throwable;

final class BuildTest extends TestCase
{
    public function testConstructor(): void
    {
        $services = new Services();
        $build = new Build($services);
        $this->assertSame(false, $build->isBuilt());
        $this->assertSame($services, $build->services());
        $this->assertInstanceOf(Path::class, $build->path());
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

    public function testMake(): void
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

        $this->assertEquals(true, $build->isBuilt());
        $this->expectException(AlreadyBuiltException::class);
        $build->make();
    }

    public function testDestroy(): void
    {
        $build = new Build(new Services());
        $build->destroy();
    }

    // public function testDestroy(): void
    // { }

    public function testNotBuiltChecksums(): void
    {
        $services = new Services();
        $build = new Build($services);
        $this->expectException(TypeError::class);
        $build->checksums();
    }

    public function testNotBuiltCheckout(): void
    {
        $services = new Services();
        $build = new Build($services);
        $this->expectException(TypeError::class);
        $build->checkout();
    }
}
