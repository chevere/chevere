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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\RouterProperties;
use Chevere\Components\Variable\VariableExport;
use Chevere\TestApp\App\Controllers\TestController;
use PHPUnit\Framework\TestCase;

final class RouterMakerTest extends TestCase
{
    public function testConstruct(): void
    {
        $routerMaker = new RouterMaker(new RouterProperties());
        $this->assertSame((new RouterProperties())->toArray(), $routerMaker->properties()->toArray());
    }

    public function testWithAddedRouteables(): void
    {
        $pathUri = '/test';
        $route = (new Route(new PathUri($pathUri)))
            ->withAddedMethod(
                new Method('POST'),
                new ControllerName(TestController::class)
            );
        $routerMaker = (new RouterMaker(new RouterProperties()))
            ->withAddedRouteable(
                new Routeable($route),
                'test'
            );
        $this->assertTrue((bool) preg_match($routerMaker->properties()->regex(), $pathUri));
        // $this->assertSame((new VariableExport($route))->toSerialize(), $routerMaker->properties()->routes()[0]);
        $this->assertSame([0], $routerMaker->properties()->groups()['test']);
    }
}
