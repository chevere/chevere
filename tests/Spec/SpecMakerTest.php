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

use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Controller\ControllerResponse;
use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Router;
use Chevere\Components\Spec\SpecMaker;
use Chevere\Components\Spec\SpecPath;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Spec\SpecInvalidArgumentException;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Tests\src\DirHelper;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class SpecMakerTest extends TestCase
{
    private DirHelper $dirHelper;

    private DirInterface $tempDir;

    private DirInterface $buildDir;

    public function setUp(): void
    {
        $this->dirHelper = new DirHelper($this);
        $this->buildDir = $this->dirHelper->dir()->getChild('build/');
    }

    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SpecMaker(
            new SpecPath('/spec'),
            $this->buildDir->getChild('spec/'),
            new Router
        );
    }

    public function testBuild(): void
    {
        $putMethod = new PutMethod;
        $getMethod = new GetMethod;
        $route = new Route(new RouteName('MyRoute'), new RoutePath('/route-path/{id:[0-9]+}'));
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint($putMethod, new SpecMakerTestPutController)
            )
            ->withAddedEndpoint(
                new RouteEndpoint($getMethod, new SpecMakerTestGetController)
            );
        $router = (new Router)
            ->withAddedRoutable(new Routable($route), 'group-name');
        $specMaker = new SpecMaker(
            new SpecPath('/spec'),
            $this->buildDir->getChild('spec/'),
            $router
        );
        $buildPath = $this->buildDir->path();
        foreach ($specMaker->files() as $jsonPath => $path) {
            $cachedFile = $buildPath->getChild(ltrim($jsonPath, '/'))->absolute();
            $this->assertFileEquals(
                $cachedFile,
                $path->absolute(),
                $cachedFile
            );
        }
        $this->assertTrue($specMaker->specIndex()->has(
            $route->name()->toString(),
            $putMethod->name()
        ));
        $this->assertTrue($specMaker->specIndex()->has(
            $route->name()->toString(),
            $getMethod->name()
        ));
    }
}

class SpecMakerTestGetController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(
                (new ControllerParameter('id', new Regex('/^[0-9]+$/')))
                    ->withDescription('The user integer id')
            )
            ->withParameter(
                (new ControllerParameter('name', new Regex('/^[\w]+$/')))
                    ->withDescription('The user name')
                    ->withIsRequired(false)
            );
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true, []);
    }
}

class SpecMakerTestPutController extends Controller
{
    public function getParameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->withParameter(
                (new ControllerParameter('id', new Regex('/^[0-9]+$/')))
                    ->withDescription('The user integer id')
            )
            ->withParameter(
                (new ControllerParameter('name', new Regex('/^[\w]+$/')))
                    ->withDescription('The user name')
                    ->withIsRequired(false)
            );
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true, []);
    }
}
