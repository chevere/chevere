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
use Chevere\Http\Methods\PatchMethod;
use Chevere\Router\Route\Route;
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Router\Route\RoutePath;
use Chevere\Spec\Specs\RouteSpec;
use Chevere\Spec\Specs\RoutableSpecs;
use Chevere\Tests\Spec\_resources\src\TestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteSpecsTest extends TestCase
{
    public function testConstruct(): void
    {
        $specs = new RoutableSpecs();
        $key = 'key';
        $this->assertCount(0, $specs);
        $this->assertFalse($specs->has($key));
        $this->expectException(OutOfBoundsException::class);
        $specs->get($key);
    }

    public function testWithPut(): void
    {
        $repository = 'repo';
        $specs = new RoutableSpecs();
        $spec = new RouteSpec(
            dirForPath("/spec/${repository}/"),
            (new Route(
                path: new RoutePath('/path'),
                name: 'test',
            ))
                ->withAddedEndpoint(
                    new RouteEndpoint(
                        new PatchMethod(),
                        new TestController()
                    )
                ),
            $repository
        );
        $specs = $specs->withPut($spec);
        $this->assertCount(1, $specs);
        $this->assertTrue($specs->has($spec->key()));
        $this->assertSame($spec, $specs->get($spec->key()));
    }
}
