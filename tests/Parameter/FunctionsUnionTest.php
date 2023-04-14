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

use function Chevere\Parameter\arrayRequired;
use function Chevere\Parameter\assertGeneric;
use function Chevere\Parameter\assertUnion;
use function Chevere\Parameter\generic;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\union;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FunctionsUnionTest extends TestCase
{
    public function testUnionArrayFixed(): void
    {
        $array = arrayRequired(a: integer());
        $union = union(arrayRequired(), $array);
        $argument = [
            'a' => 1,
        ];
        assertUnion($union, []);
        assertUnion($union, $argument);
        $union = union($array);
        assertUnion($union, $argument);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, []);
    }

    public function testUnionArrayGeneric(): void
    {
        $array = arrayRequired(a: integer());
        $generic = generic($array);
        $union = union(arrayRequired(), $generic);
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
        $union = union($generic);
        assertUnion($union, $argument);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, []);
    }

    public function testUnionGenericEmptyArray(): void
    {
        $array = arrayRequired(a: integer());
        $union = union(arrayRequired(), $array);
        $generic = generic($union);
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
        $generic = generic(union($array));
        assertGeneric($generic, $argument);
        $this->expectException(InvalidArgumentException::class);
        assertGeneric($generic, [[]]);
    }

    public function testUnionGeneric(): void
    {
        $generic = generic(integer());
        $union = union(arrayRequired(), $generic);
        assertUnion($union, []);
        assertUnion($union, [1, 2, 3]);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, [[]]);
    }

    public function testUnionGenericArray(): void
    {
        $generic = generic(arrayRequired());
        $union = union(arrayRequired(), $generic);
        assertUnion($union, []);
        assertUnion($union, [[]]);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($union, [[[]]]);
    }
}
