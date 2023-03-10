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
use function Chevere\Parameter\integerParameter;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ArgumentsGenericTest extends TestCase
{
    public function genericPropertyProvider(): array
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

    public function genericNestedPropertyProvider(): array
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
     * @dataProvider genericPropertyProvider
     */
    public function testGeneric(array $args): void
    {
        $parameters = parameters(
            top: genericParameter(
                _K: integerParameter(),
                _V: stringParameter()
            )
        );
        $this->expectNotToPerformAssertions();
        $arguments = new Arguments($parameters, ...$args);
    }

    /**
     * @dataProvider genericPropertyProvider
     */
    public function testGenericConflict(array $args): void
    {
        $parameters = parameters(
            top: genericParameter(
                _K: integerParameter(),
                _V: stringParameter('/^one$/')
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument value provided');
        $this->expectExceptionMessage("doesn't match the regex /^one$/");
        $arguments = new Arguments($parameters, ...$args);
    }

    /**
     * @dataProvider genericNestedPropertyProvider
     */
    public function testNestedGeneric(array $args): void
    {
        $parameters = parameters(
            nested: genericParameter(
                _K: integerParameter(),
                _V: genericParameter(
                    _K: stringParameter(),
                    _V: integerParameter()
                )
            )
        );
        $this->expectNotToPerformAssertions();
        $arguments = new Arguments($parameters, ...$args);
    }

    /**
     * @dataProvider genericNestedPropertyProvider
     */
    public function testNestedGenericConflict(array $args): void
    {
        $parameters = parameters(
            nested: genericParameter(
                _K: integerParameter(),
                _V: genericParameter(
                    _K: stringParameter(),
                    _V: stringParameter()
                )
            )
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expecting value of type string, int provided');
        $arguments = new Arguments($parameters, ...$args);
    }
}
