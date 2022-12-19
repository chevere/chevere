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

namespace Chevere\Tests\DataStructure\Traits;

use Chevere\Tests\DataStructure\src\UsesMapTrait;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use function Chevere\VariableSupport\deepCopy;
use PHPUnit\Framework\TestCase;
use stdClass;

final class MapTraitTest extends TestCase
{
    public function testEmpty(): void
    {
        $map = new UsesMapTrait();
        $this->assertSame(0, $map->count());
        $this->assertSame([], $map->keys());
        $this->assertCount(0, $map->getIterator());
    }

    public function testAssertHas(): void
    {
        $key = 'test';
        $object = new stdClass();
        $map = new UsesMapTrait();
        $mapClone = $map->withPut(...[
            $key => $object,
        ]);
        $mapClone->assertHas($key);
        $this->expectException(OutOfBoundsException::class);
        $mapClone->assertHas('not-found');
    }

    public function testAssertContains(): void
    {
        $key = 'test';
        $object = new stdClass();
        $map = new UsesMapTrait();
        $mapClone = $map->withPut(...[
            $key => $object,
        ]);
        $mapClone->assertContains($object);
        $this->expectException(OutOfBoundsException::class);
        $mapClone->assertContains(false);
    }

    public function testClone(): void
    {
        $map = new UsesMapTrait();
        $clone = clone $map;
        $this->assertNotSame($map, $clone);
        $this->assertNotSame($map->map(), $clone->map());
    }

    public function testWithClone(): void
    {
        $key = 'test';
        $object = new stdClass();
        $map = new UsesMapTrait();
        $mapClone = $map->withPut(...[
            $key => $object,
        ]);
        $this->assertSame(1, $mapClone->count());
        $this->assertSame([$key], $mapClone->keys());
        $this->assertNotSame($map, $mapClone);
        $this->assertNotSame($map->map(), $mapClone->map());
        $this->assertSame($key, $mapClone->find($object));
        $this->assertTrue($mapClone->contains($object));
        $mapClone->assertHas($key);
        $mapClone->assertContains($object);
        /**
         * @var string $string
         * @var object $value
         */
        foreach ($mapClone->getIterator() as $string => $value) {
            $this->assertSame($key, $string);
            $this->assertSame($object, $value);
        }
        $keyAdd = 'testAdd';
        $mapClone = $mapClone->withPut(...[
            $keyAdd => $object,
        ]);
        $this->assertSame($object, $mapClone->map()->get($keyAdd));
        $this->assertEquals($object, $mapClone->map()->get($key));
        $mapClone = $mapClone->withPut(...[
            $keyAdd => deepCopy($object),
        ]);
        $this->assertNotSame($object, $mapClone->map()->get($keyAdd));
    }
}
