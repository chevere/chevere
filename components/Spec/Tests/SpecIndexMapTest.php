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

namespace Chevere\Components\Spec\Tests;

use Chevere\Components\Route\RouteName;
use Chevere\Components\Spec\SpecIndexMap;
use Chevere\Components\Spec\SpecMethods;
use Ds\Map;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class SpecIndexMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $specIndexMap = new SpecIndexMap;
        $this->assertSame([], $specIndexMap->map()->toArray());
        $this->assertFalse($specIndexMap->hasKey('404'));
        $this->expectException(OutOfBoundsException::class);
        $specIndexMap->get('404');
    }

    public function testPut(): void
    {
        $routeName = new RouteName('route-name');
        $specMethods = new SpecMethods;
        $specIndexMap = new SpecIndexMap;
        $specIndexMap->put($routeName->toString(), $specMethods);
        $this->assertTrue($specIndexMap->hasKey($routeName->toString()));
        $this->assertSame(
            $specMethods,
            $specIndexMap->get($routeName->toString())
        );
    }
}
