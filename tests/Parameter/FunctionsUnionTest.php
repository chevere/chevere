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

namespace Chevere\Tests\Parameter;

use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertGeneric;
use function Chevere\Parameter\assertUnion;
use function Chevere\Parameter\genericp;
use function Chevere\Parameter\integerp;
use function Chevere\Parameter\unionp;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FunctionsUnionTest extends TestCase
{
    public function testUnionArrayFixed(): void
    {
        $array = arrayp(a: integerp());
        $union = unionp(arrayp(), $array);
        $argument = [
            'a' => 1,
        ];
        assertUnion($union, []);
        assertUnion($union, $argument);
        $union = unionp($array);
        assertUnion($union, $argument);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, []);
    }

    public function testUnionArrayGeneric(): void
    {
        $array = arrayp(a: integerp());
        $generic = genericp($array);
        $union = unionp(arrayp(), $generic);
        $argument = [
            [
                'a' => 1,
            ],
            [
                'a' => 2,
            ],
        ];
        assertUnion($union, []);
        assertUnion($union, $argument);
        $union = unionp($generic);
        assertUnion($union, $argument);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, []);
    }

    public function testUnionGenericEmptyArray(): void
    {
        $array = arrayp(a: integerp());
        $union = unionp(arrayp(), $array);
        $generic = genericp($union);
        $argument = [
            [
                'a' => 1,
            ],
            [
                'a' => 2,
            ],
        ];
        assertGeneric($generic, $argument);
        assertGeneric($generic, [[]]);
        $generic = genericp(unionp($array));
        assertGeneric($generic, $argument);
        $this->expectException(InvalidArgumentException::class);
        assertGeneric($generic, [[]]);
    }

    public function testUnionGeneric(): void
    {
        $generic = genericp(integerp());
        $union = unionp(arrayp(), $generic);
        assertUnion($union, []);
        assertUnion($union, [1, 2, 3]);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, [[]]);
    }

    public function testUnionGenericArray(): void
    {
        $generic = genericp(arrayp());
        $union = unionp(arrayp(), $generic);
        assertUnion($union, []);
        assertUnion($union, [[]]);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, [[[]]]);
    }
}
