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
use function Chevere\DataStructure\mapToArray;
use Chevere\Throwable\Exceptions\OutOfRangeException;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
    public function testAssertEmpty(): void
    {
        $map = new Map();
        $this->assertSame([], mapToArray($map));
        $this->expectException(OutOfRangeException::class);
        $map->assertHas('not-found');
    }

    public function testGetEmpty(): void
    {
        $map = new Map();
        $this->expectException(OutOfRangeException::class);
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
        $this->assertSame($arguments, mapToArray($map));
    }

    public function testWithPut(): void
    {
        $key = 'key';
        $value = 1234;
        $map = new Map();
        $arguments = [
            $key => $value,
        ];
        $immutable = $map->withPut(...$arguments);
        $this->assertNotSame($map, $immutable);
        $this->assertNotSame($map->keys(), $immutable->keys());
        $this->assertSame($value, $immutable->get($key));
        $immutable->assertHas($key);
        $this->assertSame($arguments, mapToArray($immutable));
    }
}
