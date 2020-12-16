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

use Chevere\Components\Http\Methods\PatchMethod;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Routable;
use Chevere\Components\Spec\SpecDir;
use Chevere\Components\Spec\Specs\RoutableSpec;
use Chevere\Components\Spec\Specs\RoutableSpecs;
use Chevere\Tests\Spec\_resources\src\TestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use function Chevere\Components\Filesystem\dirForPath;

final class RoutableSpecsTest extends TestCase
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
        $specs = new RoutableSpecs();
        $spec = new RoutableSpec(
            new SpecDir(dirForPath('/spec/group/')),
            new Routable(
                (new Route(new RoutePath('/path/')))
                    ->withAddedEndpoint(
                        new RouteEndpoint(
                            new PatchMethod(),
                            new TestController()
                        )
                    )
            )
        );
        $specs = $specs->withPut($spec);
        $this->assertCount(1, $specs);
        $this->assertTrue($specs->has($spec->key()));
        $this->assertSame($spec, $specs->get($spec->key()));
    }
}
