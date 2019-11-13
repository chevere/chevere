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
use Chevere\Components\Http\Response;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\CheckoutContract;
use Chevere\Contracts\Dir\DirContract;
use Chevere\Contracts\File\FileContract;
use PHPUnit\Framework\TestCase;

final class BuildTest extends TestCase
{
    public function getBuild(): BuildContract
    {
        $app = new App(new Services(), new Response());

        return new Build($app, new Path('build'));
    }

    public function testConstructor(): void
    {
        $build = $this->getBuild();
        $this->assertSame(false, $build->isMaked());
        $this->assertSame($build->app()->services(), $build->app()->services());
        $this->assertInstanceOf(FileContract::class, $build->file());
        $this->assertInstanceOf(DirContract::class, $build->cacheDir());
    }

    public function testWithParameters(): void
    {
        $build = $this->getBuild();
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
        $build = $this->getBuild();
        $routerMaker = new Maker();
        $build = $build
            ->withRouterMaker($routerMaker);

        $this->assertTrue($build->hasRouterMaker());
        $this->assertSame($routerMaker, $build->routerMaker());
    }

    public function testMakeWithoutRequirements(): void
    {
        $build = $this->getBuild();
        $this->expectException(LogicException::class);
        $build->make();
    }

    public function testMakeAndDestroy(): void
    {
        $build = $this->getBuild();
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
        $build = $this->getBuild();
        $this->expectException(BuildFileNotExistsException::class);
        $build->destroy();
    }

    public function testInvalidChecksumsMethodCall(): void
    {
        $build = $this->getBuild();
        $this->expectException(TypeError::class);
        $build->checksums();
    }

    public function testInvalidCheckoutMethodCall(): void
    {
        $build = $this->getBuild();
        $this->expectException(TypeError::class);
        $build->checkout();
    }
}
