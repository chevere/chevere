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
use Chevere\Router\Route\RouteEndpoint;
use Chevere\Spec\Specs\RouteEndpointSpec;
use Chevere\Spec\Specs\RouteEndpointSpecs;
use Chevere\Tests\Spec\_resources\src\TestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteEndpointSpecsTest extends TestCase
{
    public function testConstruct(): void
    {
        $specs = new RouteEndpointSpecs();
        $key = 'key';
        $this->assertCount(0, $specs);
        $this->assertFalse($specs->has($key));
        $this->expectException(OutOfBoundsException::class);
        $specs->get($key);
    }

    public function testWithPut(): void
    {
        $immutable = new RouteEndpointSpecs();
        $spec = new RouteEndpointSpec(
            dirForPath('/spec/'),
            new RouteEndpoint(
                new PatchMethod(),
                new TestController()
            )
        );
        $muted = $immutable->withPut($spec);
        $this->assertCount(0, $immutable);
        $this->assertCount(1, $muted);
        $this->assertTrue($muted->has($spec->key()));
        $this->assertSame($spec, $muted->get($spec->key()));
    }
}
