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
use function Chevere\Parameter\genericParameter;
use Chevere\Parameter\Generics;
use function Chevere\Parameter\integerParameter;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

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
            top: genericParameter(
                K: integerParameter(),
                V: stringParameter()
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
            top: genericParameter(
                K: integerParameter(),
                V: stringParameter('/^one$/')
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument value provided');
        $this->expectExceptionMessage("doesn't match the regex /^one$/");
        new Arguments($parameters, $args);
    }

    /**
     * @dataProvider genericArrayNestedPropertyProvider
     */
    public function testNestedGeneric(array $args): void
    {
        $parameters = parameters(
            nested: genericParameter(
                K: integerParameter(),
                V: genericParameter(
                    K: stringParameter(),
                    V: integerParameter()
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
            nested: genericParameter(
                K: integerParameter(),
                V: genericParameter(
                    K: stringParameter(),
                    V: stringParameter()
                )
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expecting value of type string, int provided');
        new Arguments($parameters, $args);
    }

    /**
     * @dataProvider genericArrayProvider
     */
    public function testGenericArguments(array $args): void
    {
        $parameters = new Generics(
            genericParameter(
                V: stringParameter(),
                K: stringParameter()
            )
        );
        $arguments = new Arguments($parameters, $args);
        $this->assertSame($args, $arguments->toArray());
    }

    /**
     * @dataProvider genericArrayProvider
     */
    public function testGenericArgumentsConflict(array $args): void
    {
        $parameters = new Generics(
            genericParameter(
                V: integerParameter(),
                K: stringParameter()
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^\[Property _V \*generic\]\:.*/');
        new Arguments($parameters, $args);
    }
}
