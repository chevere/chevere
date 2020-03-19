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

use Chevere\Components\Spec\SpecIndexMap;
use Chevere\Components\Spec\SpecMethods;
use Ds\Map;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class SpecIndexMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $map = new Map;
        $specIndexMap = new SpecIndexMap($map);
        $this->assertSame($map->toArray(), $specIndexMap->map()->toArray());
        $this->assertFalse($specIndexMap->hasKey(0));
        $this->expectException(OutOfBoundsException::class);
        $specIndexMap->get(0);
    }

    public function testPut(): void
    {
        $id = 100;
        $specMethods = new SpecMethods;
        $specIndexMap = (new SpecIndexMap(new Map))->withPut($id, $specMethods);
        $this->assertTrue($specIndexMap->hasKey($id));
        $this->assertSame($specMethods, $specIndexMap->get($id));
    }
}
