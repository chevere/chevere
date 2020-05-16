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

namespace Chevere\Components\Spec\Tests\Specs;

use Chevere\Components\Http\Methods\PatchMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Spec\Specs\RouteableSpec;
use Chevere\Components\Spec\Specs\RouteableSpecs;
use Chevere\TestApp\App\Controllers\TestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteableSpecsTest extends TestCase
{
    public function testConstruct(): void
    {
        $specs = new RouteableSpecs;
        $key = 'key';
        $this->assertCount(0, $specs->map());
        $this->assertFalse($specs->hasKey($key));
        $this->expectException(OutOfBoundsException::class);
        $specs->get($key);
    }

    public function testWithPut(): void
    {
        $specs = new RouteableSpecs;
        $spec = new RouteableSpec(
            new SpecPath('/spec/group'),
            new Routable(
                (new Route(new RouteName('name'), new RoutePath('/path')))
                    ->withAddedEndpoint(
                        new RouteEndpoint(
                            new PatchMethod,
                            new TestController
                        )
                    )
            )
        );
        $specs->put($spec);
        $this->assertCount(1, $specs->map());
        $this->assertTrue($specs->hasKey($spec->key()));
        $this->assertSame($spec, $specs->get($spec->key()));
    }
}
