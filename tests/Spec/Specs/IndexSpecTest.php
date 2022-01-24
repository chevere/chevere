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

use function Chevere\Filesystem\dirForPath;
use Chevere\Http\Methods\GetMethod;
use Chevere\Router\Route\Route;
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Router\Route\RoutePath;
use Chevere\Spec\Specs\GroupSpec;
use Chevere\Spec\Specs\IndexSpec;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;
use SplObjectStorage;

final class IndexSpecTest extends TestCase
{
    public function testConstruct(): void
    {
        $spec = new IndexSpec(dirForPath('/spec/'));
        $this->assertSame([
            'repositories' => [],
        ], $spec->toArray());
    }

    public function testWithAddedGroup(): void
    {
        $routePath = new RoutePath('/route/path');
        $specDir = dirForPath('/spec/');
        $repository = 'repo';
        $route = (new Route('test', $routePath))
            ->withAddedEndpoint(
                new RouteEndpoint(new GetMethod(), new TestController())
            );
        $objectStorage = new SplObjectStorage();
        $objectStorage->attach($route);
        $groupSpec = new GroupSpec($specDir, $repository);
        $spec = (new IndexSpec($specDir))->withAddedGroup($groupSpec);
        $this->assertSame(
            $specDir->path()->__toString() . 'index.json',
            $spec->jsonPath()
        );
        $this->assertSame(
            [
                'repositories' => [
                    $groupSpec->key() => $groupSpec->toArray(),
                ],
            ],
            $spec->toArray()
        );
    }
}
