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

namespace Chevere\Tests\Spec;

use function Chevere\Components\Filesystem\dirForPath;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteEndpoint;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Router\Router;
use Chevere\Components\Spec\SpecMaker;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Tests\Spec\_resources\src\SpecMakerTestGetController;
use Chevere\Tests\Spec\_resources\src\SpecMakerTestPutController;
use Chevere\Tests\src\DirHelper;
use PHPUnit\Framework\TestCase;

final class SpecMakerTest extends TestCase
{
    private DirHelper $dirHelper;

    private DirInterface $buildDir;

    protected function setUp(): void
    {
        $this->dirHelper = new DirHelper($this);
        $this->buildDir = $this->dirHelper->dir()->getChild('build/');
    }

    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SpecMaker(
            dirForPath('/spec/'),
            $this->buildDir->getChild('spec/'),
            new Router()
        );
    }

    public function testBuild(): void
    {
        $putMethod = new PutMethod();
        $getMethod = new GetMethod();
        $route = new Route(
            new RoutePath('/route-path/{id:[0-9]+}')
        );
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint($putMethod, new SpecMakerTestPutController())
            )
            ->withAddedEndpoint(
                new RouteEndpoint($getMethod, new SpecMakerTestGetController())
            );
        $router = (new Router())
            ->withAddedRoutable(new Routable($route), 'repo');
        $specMaker = new SpecMaker(
            dirForPath('/spec/'),
            $this->buildDir->getChild('spec/'),
            $router
        );
        $buildPath = $this->buildDir->path();
        /**
         * @var PathInterface $path
         */
        foreach ($specMaker->files() as $jsonPath => $path) {
            $cachedFile = $buildPath->getChild(ltrim($jsonPath, '/'))->toString();
            $this->assertFileEquals(
                $cachedFile,
                $path->toString(),
                $cachedFile
            );
        }
        $this->assertTrue($specMaker->specIndex()->has(
            $route->path()->toString(),
            $putMethod->name()
        ));
        $this->assertTrue($specMaker->specIndex()->has(
            $route->path()->toString(),
            $getMethod->name()
        ));
    }
}
