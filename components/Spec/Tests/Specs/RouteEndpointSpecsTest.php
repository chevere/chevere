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
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Spec\SpecPath;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Components\Spec\Specs\RouteEndpointSpecs;
use Chevere\TestApp\App\Controllers\TestController;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class RouteEndpointSpecsTest extends TestCase
{
    public function testConstruct(): void
    {
        $specs = new RouteEndpointSpecs;
        $key = 'key';
        $this->assertCount(0, $specs->map());
        $this->assertFalse($specs->hasKey($key));
        $this->expectException(OutOfBoundsException::class);
        $specs->get($key);
    }

    public function testWithPut(): void
    {
        $inmutable = new RouteEndpointSpecs;
        $spec = new RouteEndpointSpec(
            new SpecPath('/spec'),
            new RouteEndpoint(
                new PatchMethod,
                new TestController
            )
        );
        $muted = $inmutable->withPut($spec);
        $this->assertCount(0, $inmutable->map());
        $this->assertCount(1, $muted->map());
        $this->assertTrue($muted->hasKey($spec->key()));
        $this->assertSame($spec, $muted->get($spec->key()));
    }
}
