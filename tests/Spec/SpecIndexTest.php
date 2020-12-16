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

use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Spec\SpecDir;
use Chevere\Components\Spec\SpecIndex;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Tests\Spec\_resources\src\TestController;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\dirForPath;

final class SpecIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $specIndex = new SpecIndex();
        $method = new GetMethod();
        $this->assertFalse($specIndex->has('404', $method::name()));
        $this->assertCount(0, $specIndex);
        $this->expectException(OutOfBoundsException::class);
        $specIndex->get('404', $method::name());
    }

    public function testWithOffset(): void
    {
        $routeName = new RouteName('repo:/path/');
        $method = new GetMethod();
        $routeEndpoint = new RouteEndpoint($method, new TestController());
        $specPath = new SpecDir(dirForPath('/spec/group/route/'));
        $routeEndpointSpec = new RouteEndpointSpec($specPath, $routeEndpoint);
        $specIndex = (new SpecIndex())->withAddedRoute(
            $routeName->toString(),
            $routeEndpointSpec
        );
        $this->assertFalse($specIndex->has('404', $method->name()));
        $this->assertTrue($specIndex->has(
            $routeName->toString(),
            $method->name()
        ));
        $this->assertCount(1, $specIndex);
        $this->assertSame(
            $specPath->getChild($method->name() . '/')->toString() . '.json',
            $specIndex->get(
                $routeName->toString(),
                $method->name()
            )
        );
        $method2 = new PutMethod();
        $routeEndpoint2 = new RouteEndpoint($method2, new TestController());
        $routeEndpointSpec2 = new RouteEndpointSpec($specPath, $routeEndpoint2);
        $specIndex = $specIndex->withAddedRoute(
            $routeName->toString(),
            $routeEndpointSpec2
        );
        $this->assertTrue($specIndex->has(
            $routeName->toString(),
            $method->name()
        ));
        $this->assertTrue($specIndex->has(
            $routeName->toString(),
            $method2->name()
        ));
        $this->assertCount(1, $specIndex);
        $this->assertSame(
            $specPath->getChild($method2->name() . '/')->toString() . '.json',
            $specIndex->get($routeName->toString(), $method2->name())
        );
    }
}
