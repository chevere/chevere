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

namespace Chevere\Tests\DataStructure;

use Chevere\DataStructure\Map;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
    public function testAssertEmpty(): void
    {
        $map = new Map();
        $this->assertSame([], $map->toArray());
        $this->expectException(OutOfBoundsException::class);
        $map->assertHas('not-found');
    }

    public function testGetEmpty(): void
    {
        $map = new Map();
        $this->expectException(OutOfBoundsException::class);
        $map->get('not-found');
    }

    public function testConstructWithArguments(): void
    {
        $arguments = [
            'test' => 123,
            'some' => 'thing',
        ];
        $map = new Map(...$arguments);
        foreach ($arguments as $name => $value) {
            $this->assertSame($value, $map->get($name));
        }
        $this->assertSame($arguments, $map->toArray());
    }

    public function testWithPut(): void
    {
        $key = 'key';
        $value = 1234;
        $map = new Map();
        $arguments = [
            $key => $value,
        ];
        $mapWith = $map->withPut($key, $value);
        $this->assertNotSame($map, $mapWith);
        $this->assertNotSame($map->keys(), $mapWith->keys());
        $this->assertSame($value, $mapWith->get($key));
        $mapWith->assertHas($key);
        $this->assertSame($arguments, $mapWith->toArray());
    }

    public function testWithPutConsecutiveNamed(): void
    {
        $map = new Map();
        $mapWith = $map
            ->withPut('a', 'a')
            ->withPut('b', 'b');
        $this->assertCount(2, $mapWith);
    }

    public function testWithPutNumericVariadic(): void
    {
        $map = new Map();
        foreach ([128, 256] as $item) {
            $map = $map->withPut(strval($item), $item);
        }
        $this->assertCount(2, $map);
    }

    public function testWithOut(): void
    {
        $map = (new Map())
            ->withPut('a', 'foo')
            ->withPut('b', 'bar');
        $mapWith = $map->without('a');
        $this->assertNotSame($map, $mapWith);
        $this->assertFalse($mapWith->has('a'));
        $this->assertSame(['b'], $mapWith->keys());
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Key `a` not found');
        $mapWith->without('a');
    }

    public function testArrayKeys(): void
    {
        $map = (new Map())
            ->withPut('', 'empty')
            ->withPut(0, 'zero');
        $array = [
            '' => 'empty',
            0 => 'zero',
        ];
        $this->assertSame(array_keys($array), $map->keys());
        $this->assertSame($array[''], $map->get(''));
        $this->assertSame($array[0], $map->get(0));
        $this->assertFalse($map->has('0'));
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Key `0` not found');
        $map->get('0');
    }
}
