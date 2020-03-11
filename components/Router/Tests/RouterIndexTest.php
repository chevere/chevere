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

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Router\Properties;

use BadMethodCallException;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\RouterIndex;
use PHPUnit\Framework\TestCase;

final class RouterIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $routePath = new RoutePath('/path');
        $routerIndex = new RouterIndex();
        $this->assertSame([], $routerIndex->toArray());
        $this->assertFalse($routerIndex->has($routePath));
        $this->expectException(BadMethodCallException::class);
        $routerIndex->get($routePath);
    }

    public function testWithAdded(): void
    {
        $path = '/path';
        $id = 0;
        $group = 'some-group';
        $name = 'some-name';
        $routePath = new RoutePath($path);
        $route = new Route(
            new RouteName($name),
            $routePath
        );
        $routerIndex = (new RouterIndex())
            ->withAdded($route, $id, $group);
        $this->assertTrue($routerIndex->has($routePath));
        $this->assertSame([
            $path => [
                'id' => $id,
                'group' => $group,
                'name' => $name,
            ]
        ], $routerIndex->toArray());
    }
}
