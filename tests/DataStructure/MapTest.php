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

use Chevere\Components\DataStructure\Map;
use Chevere\Exceptions\Core\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
    public function testAssertEmpty(): void
    {
        $map = new Map(...[]);
        $this->expectException(OutOfBoundsException::class);
        $map->assertHasKey('not-found');
    }

    public function testGetEmpty(): void
    {
        $map = new Map(...[]);
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
        $this->assertSame($arguments, $map->map()->toArray());
        foreach ($arguments as $name => $value) {
            $this->assertSame($value, $map->get($name));
        }
    }

    public function testWithPut(): void
    {
        $key = 'key';
        $value = 1234;
        $map = new Map(...[]);
        $dsMap = $map->map();
        $immutable = $map->withPut(...[
            $key => $value,
        ]);
        $this->assertNotSame($dsMap, $immutable->map());
        $this->assertNotSame($map, $immutable);
        $this->assertNotSame($map->keys(), $immutable->keys());
        $this->assertSame($value, $immutable->get($key));
        $immutable->assertHasKey($key);
    }
}
