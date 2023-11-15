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

use Chevere\DataStructure\Vector;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VectorTest extends TestCase
{
    public function testAssertEmpty(): void
    {
        $vector = new Vector();
        $this->assertCount(0, $vector);
        $this->expectException(OutOfBoundsException::class);
        $vector->assertHas(0);
    }

    public function testGetEmpty(): void
    {
        $vector = new Vector();
        $this->assertFalse($vector->has(0));
        $this->assertFalse($vector->contains(0));
        $this->assertSame(null, $vector->find(0));
        $this->expectException(OutOfBoundsException::class);
        $vector->get(0);
    }

    public function testConstructWithArguments(): void
    {
        $arguments = [
            0 => 123,
            1 => 'thing',
            2 => new stdClass(),
        ];
        $vector = new Vector(...$arguments);
        foreach ($arguments as $pos => $value) {
            $vector->assertHas($pos);
            $this->assertTrue($vector->has($pos));
            $this->assertTrue($vector->contains($value));
            $this->assertSame($value, $vector->get($pos));
            $this->assertSame($pos, $vector->find($value));
        }
        $this->assertTrue($vector->contains(...$arguments));
        $this->assertSame($arguments, $vector->toArray());
    }

    public function testWithPush(): void
    {
        $values = [1];
        $vector = new Vector();
        $immutable = $vector->withPush(...$values);
        $this->assertCount(count($values), $immutable);
        $this->assertNotSame($vector, $immutable);
        $this->assertSame($values, $immutable->toArray());
    }

    public function testWithSet(): void
    {
        $init = ['uno', 'dos', 'tres'];
        $value = 2;
        $vector = new Vector(...$init);
        $immutable = $vector->withSet(1, $value);
        $this->assertNotSame($vector, $immutable);
        $this->assertSame($vector->keys(), $immutable->keys());
        $immutable->assertHas(1);
        $this->assertSame($value, $immutable->get(1));
        $this->expectException(OutOfBoundsException::class);
        $vector->withSet(count($init), 'fail');
    }

    public function testWithUnshift(): void
    {
        $values = [1, 2, 999];
        $unshift = [999, 888];
        $vector = new Vector(...$values);
        $this->assertCount(count($values), $vector);
        $immutable = $vector->withUnshift(...$unshift);
        $this->assertNotSame($vector, $immutable);
        $this->assertCount(count($values) + count($unshift), $immutable);
        $expected = array_merge($unshift, $values);
        $this->assertSame($expected, $immutable->toArray());
    }

    public function testWithRemove(): void
    {
        $values = [1, 2, 3];
        $vector = new Vector(...$values);
        $immutable = $vector->withRemove(1);
        $this->assertNotSame($vector, $immutable);
        $this->assertCount(count($values) - 1, $immutable);
        unset($values[1]);
        $values = array_values($values);
        $this->assertSame(array_keys($values), $immutable->keys());
        $this->assertSame($values, $immutable->toArray());
        $this->expectException(OutOfBoundsException::class);
        $immutable->withRemove(count($values) + 1);
    }

    public function testWithInsert(): void
    {
        $values = [1, 2, 3];
        $vector = new Vector(...$values);
        $immutable = $vector->withInsert(1, 11);
        $this->assertNotSame($vector, $immutable);
        $this->assertCount(count($values) + 1, $immutable);
        array_splice($values, 1, 0, 11);
        $values = array_values($values);
        $this->assertSame(array_keys($values), $immutable->keys());
        $this->assertSame($values, $immutable->toArray());
        $this->expectException(OutOfBoundsException::class);
        $immutable->withInsert(count($values) + 1, 'fail');
    }
}
