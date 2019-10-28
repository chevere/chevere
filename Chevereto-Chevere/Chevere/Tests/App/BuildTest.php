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
use Chevere\Components\App\Services;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Router;
use PHPUnit\Framework\TestCase;

final class BuildTest extends TestCase
{
    public function testConstructor(): void
    {
        $services = new Services();
        $build = new Build($services);
        $this->assertInstanceOf(Path::class, $build->path());
        $this->assertSame(false, $build->isBuilt());
        $this->assertSame($services, $build->services());
    }

    public function testWithServices(): void
    {
        $baseServices = new Services();
        $build = new Build($baseServices);
        $newServices = (new Services())
            ->withApi(new Api())
            ->withRouter(new Router());
        $build = $build
            ->withServices($newServices);
        $this->assertSame($newServices, $build->services());
    }
}
