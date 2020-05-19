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
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\DirFromString;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterMaker;
use Chevere\Tests\Router\CacheHelper;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\SpecMaker;
use Chevere\Components\Spec\SpecPath;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class SpecMakerTest extends TestCase
{
    private CacheHelper $cacheHelper;

    public function setUp(): void
    {
        $this->cacheHelper = new CacheHelper(__DIR__, $this);
    }

    public function tearDown(): void
    {
        $this->cacheHelper->tearDown();
    }

    public function testConstructInvalidArgument(): void
    {
        $shortName = (new ReflectionObject($this))->getShortName();
        $this->expectException(SpecInvalidArgumentException::class);
        new SpecMaker(
            new SpecPath('/spec'),
            new DirFromString(__DIR__ . "/_resources/$shortName/spec/"),
            new Router
        );
    }

    public function testConstruct(): void
    {
        $putMethod = new PutMethod;
        $getMethod = new GetMethod;
        $route = new Route(new RouteName('route-name'), new RoutePath('/route-path/{id}'));
        $route = $route
            ->withAddedEndpoint(
                new RouteEndpoint($putMethod, new SpecMakerTestPutController)
            )
            ->withAddedEndpoint(
                new RouteEndpoint($getMethod, new SpecMakerTestGetController)
            );
        $routerMaker = (new RouterMaker)
            ->withAddedRoutable(new Routable($route), 'group-name');
        $specMaker = new SpecMaker(
            new SpecPath('/spec'),
            $this->cacheHelper->getWorkingDir()->getChild('spec/'),
            $routerMaker->router()
        );
        $cachedPath = $this->cacheHelper->getCachedDir()->path();
        foreach ($specMaker->files() as $jsonPath => $path) {
            $cachedFile = $cachedPath->getChild(ltrim($jsonPath, '/'))->absolute();
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
            );
    }

    public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface
    {
        return new ControllerResponse(true);
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
        return new ControllerResponse(true);
    }
}
