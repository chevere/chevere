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

namespace Chevere\Tests\Spec\Specs;

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Spec\SpecDir;
use Chevere\Components\Spec\Specs\GroupSpec;
use Chevere\Components\Spec\Specs\IndexSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;
use SplObjectStorage;
use function Chevere\Components\Filesystem\dirForPath;

final class IndexSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $spec = new IndexSpec(new SpecDir(dirForPath('/spec/')));
        $this->assertSame([
            'groups' => []
        ], $spec->toArray());
    }

    public function testWithAddedGroup(): void
    {
        $routePath = new RoutePath('/route/path');
        $specPath = new SpecDir(dirForPath('/spec/'));
        $groupName = 'group-name';
        $route = (new Route($routePath))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod(), new TestController())
            );
        $objectStorage = new SplObjectStorage();
        $objectStorage->attach(new Routable($route));
        $groupSpec = new GroupSpec($specPath, $groupName);
        $spec = (new IndexSpec($specPath))->withAddedGroup($groupSpec);
        $this->assertSame($specPath->toString() . 'index.json', $spec->jsonPath());
        $this->assertSame(
            [
                'groups' => [
                    $groupSpec->key() => $groupSpec->toArray()
                ],
            ],
            $spec->toArray()
        );
    }
}
