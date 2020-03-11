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

namespace Chevere\Components\Spec\Tests;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterGroups;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterNamed;
use Chevere\Components\Router\RoutesCache;
use Chevere\Components\Spec\Exceptions\RouterMissingPropertyException;
use Chevere\Components\Spec\Spec;
use PHPUnit\Framework\TestCase;

final class SpecTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $this->expectException(RouterMissingPropertyException::class);
        new Spec($this->getEmptyRouter());
    }

    // public function testConstruct(): void
    // {
    //     $router = $this->getCachedRouter();
    //     // xdd($router);
    // }

    private function getEmptyRouter(): RouterInterface
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/empty/'));
        $cache = new Cache($dir);
        $routesCache = new RoutesCache($cache);

        return new Router($routesCache);
    }

    private function getCachedRouter(): RouterInterface
    {
        $dir = new Dir(new Path(__DIR__ . '/_resources/cached/'));
        $cache = new Cache($dir);
        $routesCache = new RoutesCache($cache);

        return new Router($routesCache);
    }
}
