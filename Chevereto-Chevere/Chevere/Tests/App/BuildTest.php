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

use Chevere\Components\Api\Api;
use Chevere\Components\App\Build;
use Chevere\Components\App\Parameters;
use Chevere\Components\App\Services;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Router;
use Exception;
use PHPUnit\Framework\TestCase;
use TypeError;

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

    public function testMake(): void
    {
    }

    public function testDestroy(): void
    {
    }

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
