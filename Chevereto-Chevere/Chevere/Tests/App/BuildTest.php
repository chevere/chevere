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

use Error;
use LogicException;
use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Exceptions\BuildFileNotExistsException;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Http\Response;
use Chevere\Components\Path\PathApp;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\App\Contracts\BuildContract;
use Chevere\Components\App\Contracts\CheckoutContract;
use Chevere\Components\App\Contracts\ParametersContract;
use Chevere\Components\Dir\Contracts\DirContract;
use Chevere\Components\File\Contracts\FileContract;
use PHPUnit\Framework\TestCase;

final class BuildTest extends TestCase
{
    public function getBuild(): BuildContract
    {
        $app = new App(new Services(), new Response());

        return new Build($app);
    }

    public function getParameters(): ParametersContract
    {
        return
            new Parameters(
                new ArrayFile(
                    new FilePhp(
                        new File(
                            new PathApp('parameters.php')
                        )
                    )
                )
            );
    }

    public function testConstructor(): void
    {
        $build = $this->getBuild();
        $this->assertSame(false, $build->isMaked());
        $this->assertSame($build->app()->services(), $build->app()->services());
        $this->assertInstanceOf(FileContract::class, $build->file());
        $this->assertInstanceOf(DirContract::class, $build->dir());
    }

    public function testWithParameters(): void
    {
        $build = $this->getBuild();
        $parameters = $this->getParameters();
        $build = $build
            ->withParameters($parameters);

        $this->assertTrue($build->hasParameters());
        $this->assertSame($parameters, $build->parameters());
    }

    public function testWithRouterMaker(): void
    {
        $build = $this->getBuild();
        $routerMaker = new RouterMaker();
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
        $parameters = $this->getParameters();
        $routerMaker = new RouterMaker();
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
        $this->assertSame([], $build->checksums());
        $build->checksums();
    }

    public function testInvalidCheckoutMethodCall(): void
    {
        $build = $this->getBuild();
        $this->expectException(Error::class);
        $build->checkout();
    }
}
