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

use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Routing\FsRoutesMaker;
use Chevere\Components\Routing\Routing;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\RouterInterface;
use PHPUnit\Framework\TestCase;

final class RoutingTest extends TestCase
{
    private DirInterface $cacheDir;

    private DirInterface $routesDir;

    public function setUp(): void
    {
        $resourcesDir = (new FilesystemFactory)
            ->getDirFromString(__DIR__ . '/_resources/');
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
        $routing = new Routing($fsRoutesMaker, new Router);
        $this->assertInstanceOf(RouterInterface::class, $routing->router());
    }
}
