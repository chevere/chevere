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

namespace Chevere\Tests\Routing;

use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Routing\FsRoutesMaker;
use Chevere\Components\Routing\Routing;
use PHPUnit\Framework\TestCase;

final class RoutingTest extends TestCase
{

    private DirInterface $cacheDir;

    private DirInterface $routesDir;

    public function setUp(): void
    {
        $resourcesDir = new DirFromString(__DIR__ . '/_resources/');
        $this->cacheDir = $resourcesDir->getChild('cache/');
        $this->routesDir = $resourcesDir->getChild('routes/');
        if (!$this->cacheDir->exists()) {
            $this->cacheDir->create(0777);
        }
    }

    public function tearDown(): void
    {
        $this->cacheDir->removeContents();
    }

    public function testConstruct(): void
    {
        $fsRoutesMaker = new FsRoutesMaker($this->routesDir);
        $routerMaker = new RouterMaker;
        $routing = new Routing($fsRoutesMaker, $routerMaker);
        $this->assertInstanceOf(RouterInterface::class, $routing->router());
    }
}
