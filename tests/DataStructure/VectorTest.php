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
use Chevere\Throwable\Exceptions\OutOfRangeException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VectorTest extends TestCase
{
    public function testAssertEmpty(): void
    {
        $vector = new Vector();
        $this->assertCount(0, $vector);
        $this->expectException(OutOfRangeException::class);
        $vector->assertHas(0);
    }

    public function testGetEmpty(): void
    {
        $vector = new Vector();
        $this->assertFalse($vector->has(0));
        $this->assertSame(null, $vector->find(0));
        $this->expectException(OutOfRangeException::class);
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
            $this->assertSame($value, $vector->get($pos));
            $this->assertSame($pos, $vector->find($value));
        }
        $array = iterator_to_array($vector->getIterator());
        $this->assertSame($arguments, $array);
    }

    public function testWithPush(): void
    {
        $values = [1];
        $vector = new Vector();
        $immutable = $vector->withPush(...$values);
        $this->assertCount(count($values), $immutable);
        $this->assertNotSame($vector, $immutable);
        $array = iterator_to_array($immutable->getIterator());
        $this->assertSame($values, $array);
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
        $this->expectException(OutOfRangeException::class);
        $vector->withSet(count($init), 'fail');
    }

    public function testWithUnshift(): void
    {
        $values = [1, 2, 999];
        $unshift = [999, 888];
        $vector = new Vector(...$values);
        $this->assertCount(count($values), $vector);
        $immutable = $vector->withUnshift(...$unshift);
        $this->assertCount(count($values) + count($unshift), $immutable);
        $this->assertNotSame($vector, $immutable);
        $array = iterator_to_array($immutable->getIterator());
        $expected = array_merge($unshift, $values);
        $this->assertSame($expected, $array);
    }
}
