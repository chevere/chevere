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

use Chevere\Parameter\Arguments;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\assertGeneric;
use function Chevere\Parameter\generic;
use function Chevere\Parameter\int;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;

final class ArgumentsGenericTest extends TestCase
{
    public function genericArrayProvider(): array
    {
        return [
            [
                [
                    'a' => 'foo',
                    'b' => 'bar',
                ],
            ],
        ];
    }

    public function genericArrayPropertyProvider(): array
    {
        return [
            [
                [
                    'top' => [
                        1 => 'one',
                        2 => 'two',
                    ],
                ],
            ],
        ];
    }

    public function genericArrayNestedPropertyProvider(): array
    {
        return [
            [
                [
                    'nested' => [
                        1 => [
                            'foo' => 1,
                            'bar' => 2,
                        ],
                        2 => [
                            'wea' => 3,
                            'baz' => 4,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider genericArrayPropertyProvider
     */
    public function testGeneric(array $args): void
    {
        $parameters = parameters(
            top: generic(
                K: int(),
                V: string()
            )
        );
        $this->expectNotToPerformAssertions();
        new Arguments($parameters, $args);
    }

    /**
     * @dataProvider genericArrayPropertyProvider
     */
    public function testGenericConflict(array $args): void
    {
        $parameters = parameters(
            top: generic(
                K: int(),
                V: string('/^one$/')
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument value provided');
        $this->expectExceptionMessage("doesn't match the regex `/^one$/`");
        new Arguments($parameters, $args);
    }

    /**
     * @dataProvider genericArrayNestedPropertyProvider
     */
    public function testNestedGeneric(array $args): void
    {
        $parameters = parameters(
            nested: generic(
                K: int(),
                V: generic(
                    K: string(),
                    V: int()
                )
            )
        );
        $this->expectNotToPerformAssertions();
        new Arguments($parameters, $args);
    }

    /**
     * @dataProvider genericArrayNestedPropertyProvider
     */
    public function testNestedGenericConflict(array $args): void
    {
        $parameters = parameters(
            nested: generic(
                K: int(),
                V: generic(
                    K: string(),
                    V: string()
                )
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must be of type Stringable|string, int given');
        new Arguments($parameters, $args);
    }

    /**
     * @dataProvider genericArrayProvider
     */
    public function testGenericArguments(array $args): void
    {
        $parameter = generic(
            V: string(),
            K: string()
        );
        $this->expectNotToPerformAssertions();
        assertGeneric($parameter, $args);
    }

    /**
     * @dataProvider genericArrayProvider
     */
    public function testGenericArgumentsConflict(array $args): void
    {
        $parameter = generic(
            V: int(),
            K: string()
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^\[_V \*generic\]\:.*/');
        assertGeneric($parameter, $args);
    }
}
