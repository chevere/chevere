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

namespace Chevere\Components\Spec\Specs\Tests;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouteableObjectsRead;
use Chevere\Components\Spec\GroupSpec;
use Chevere\Components\Spec\IndexSpec;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;
use SplObjectStorage;

final class IndexSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $spec = new IndexSpec('/spec/');
        $this->assertSame([
            'groups' => []
        ], $spec->toArray());
        $this->assertCount(0, $spec->objects());
    }

    public function testWithAddedGroup(): void
    {
        $routeName = new RouteName('route-name');
        $routePath = new RoutePath('/route/path');
        $specPath = '/spec/';
        $groupName = 'group-name';
        $groupSpecPath = $specPath . $groupName . '/';
        $route = (new Route($routeName, $routePath))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod, new TestController)
            );
        $objectStorage = new SplObjectStorage;
        $objectStorage->attach(new Routeable($route));
        $objects = new RouteableObjectsRead($objectStorage);
        $groupSpec = new GroupSpec($groupSpecPath, $objects);
        $spec = (new IndexSpec($specPath))->withAddedGroup($groupSpec);
        $this->assertSame($specPath . 'index.json', $spec->jsonPath());
        $this->assertSame(
            [
                'groups' => [$groupSpec->toArray()],
            ],
            $spec->toArray()
        );
        $this->assertCount(1, $spec->objects());
        $spec->objects()->rewind();
        $object = $spec->objects()->current();
        $this->assertNull($spec->objects()->getInfo());
        $this->assertSame($groupSpec->jsonPath(), $object->jsonPath());
        $this->assertSame($groupSpec->toArray(), $object->toArray());
    }
}
