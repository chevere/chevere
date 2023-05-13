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
use Chevere\Throwable\Exceptions\OutOfBoundsException;
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
        $mapWith = $map->withPut(...$arguments);
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
            ->withPut(a: 'a')
            ->withPut(b: 'b');
        $this->assertCount(2, $mapWith);
    }

    public function testWithPutConsecutivePositional(): void
    {
        $map = new Map();
        $mapWith = $map
            ->withPut(...['a'])
            ->withPut(...['b']);
        $this->assertCount(1, $mapWith);
    }

    public function testWithOut(): void
    {
        $map = (new Map())->withPut(a: 'foo', b: 'bar');
        $mapWith = $map->without('a');
        $this->assertNotSame($map, $mapWith);
        $this->assertFalse($mapWith->has('a'));
        $this->assertSame(['b'], $mapWith->keys());
        $this->expectException(OutOfBoundsException::class);
        $mapWith->without('a');
    }
}
