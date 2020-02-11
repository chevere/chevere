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

use Error;
use LogicException;
use Chevere\Components\App\App;
use Chevere\Components\App\Build;
use Chevere\Components\App\Exceptions\BuildFileNotExistsException;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Http\Response;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\App\Interfaces\BuildInterface;
use Chevere\Components\App\Interfaces\CheckoutInterface;
use Chevere\Components\App\Interfaces\ParametersInterface;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use PHPUnit\Framework\TestCase;

final class BuildTest extends TestCase
{
    public function getBuild(): BuildInterface
    {
        $app = new App(new Services(), new Response());

        return new Build($app);
    }

    public function getParameters(): ParametersInterface
    {
        return
            new Parameters(
                new ArrayFile(
                    new PhpFile(
                        new File(
                            new AppPath('parameters.php')
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
        $this->assertInstanceOf(FileInterface::class, $build->file());
        $this->assertInstanceOf(DirInterface::class, $build->dir());
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

    public function testMakeWithoutRequirements(): void
    {
        $build = $this->getBuild();
        $this->expectException(LogicException::class);
        $build->make();
    }

    // public function testMakeAndDestroy(): void
    // {
    //     $build = $this->getBuild();
    //     $parameters = $this->getParameters();
    //     $routerMaker = new RouterMaker(new RouterProperties());
    //     $build = $build
    //         ->withParameters($parameters)
    //         ->withRouterMaker($routerMaker)
    //         ->make();

    //     $this->assertEquals(true, $build->isMaked());
    //     $this->assertIsArray($build->checksums());
    //     $this->assertInstanceOf(CheckoutInterface::class, $build->checkout());
    //     $build->destroy();
    // }

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
