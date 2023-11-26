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

namespace Chevere\Tests\Parameter\Attribute;

use Chevere\Tests\Parameter\src\UsesParameterAttributes;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ParameterAttributesTest extends TestCase
{
    public function dataProviderWillSuccess(): array
    {
        return [
            [
                'name' => 'Rodolfo',
                'age' => 25,
                'array' => [
                    'id' => 1,
                ],
                'iterable' => ['Chevere', 'Chevere', 'Chevere', 'Uh'],
            ],
        ];
    }

    public function dataProviderWillFail(): array
    {
        return [
            [
                'name' => 'Peoples Hernandez',
                'age' => 66,
                'array' => [
                    'id' => 1,
                ],
                'iterable' => ['people'],
                'error' => "Argument value provided `Peoples Hernandez` doesn't match the regex `/^[A-Za-z]+$/`",
            ],
            [
                'name' => 'zerothehero',
                'age' => 0,
                'array' => [
                    'id' => 1,
                ],
                'iterable' => ['zero'],
                'error' => 'Argument value provided `0` is less than `1`',
            ],
            [
                'name' => 'SergioDalmata',
                'age' => 101,
                'array' => [
                    'id' => 1,
                ],
                'iterable' => ['dalmata'],
                'error' => 'Argument value provided `101` is greater than `100`',
            ],
            [
                'name' => 'DonZeroId',
                'age' => 42,
                'array' => [
                    'id' => 0,
                ],
                'iterable' => ['zeroid'],
                'error' => '[id]: Argument value provided `0` is less than `1`',
            ],
            [
                'name' => 'iterableNull',
                'age' => 24,
                'array' => [
                    'id' => 42,
                ],
                'iterable' => [123],
                'error' => 'Argument #1 ($value) must be of type Stringable|string, int given',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderWillFail
     */
    public function testWillFail(
        string $name,
        int $age,
        array $array,
        iterable $iterable,
        string $error
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($error);

        new UsesParameterAttributes($name, $age, $array, $iterable);
    }

    /**
     * @dataProvider dataProviderWillSuccess
     */
    public function testWillSuccess(
        string $name,
        int $age,
        array $array,
        iterable $iterable
    ): void {
        $this->expectNotToPerformAssertions();

        new UsesParameterAttributes($name, $age, $array, $iterable);
    }
}
