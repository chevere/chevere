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
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\IntParameter;
use Chevere\Parameter\Parameters;
use Chevere\Regex\Regex;
use Chevere\Tests\Parameter\_resources\ArrayAccessDynamic;
use Chevere\Tests\Parameter\_resources\ArrayAccessMixed;
use Chevere\Tests\Parameter\_resources\ArrayAccessScoped;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\boolean;
use function Chevere\Parameter\int;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\string;

final class ArgumentsTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $parameters = parameters(test: string());
        $this->assertTrue($parameters->isRequired('test'));
        $this->assertFalse($parameters->isOptional('test'));
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, [
            'test' => 123,
        ]);
    }

    public function testConstruct(): void
    {
        $args = [
            'id' => 1,
            'name' => 'someValue',
        ];
        $parameters = parameters(
            id: new IntParameter(),
            name: string()
        );
        $arguments = new Arguments($parameters, $args);
        $this->assertSame($parameters, $arguments->parameters());
        $this->assertSame($args, $arguments->toArray());
        foreach ($args as $name => $value) {
            $this->assertTrue($arguments->has($name));
            $this->assertSame($value, $arguments->get($name));
        }
        $notFoundKey = '404';
        $this->assertFalse($arguments->has($notFoundKey));
        $this->expectException(OutOfBoundsException::class);
        $arguments->get($notFoundKey);
    }

    public function testMissingArgument(): void
    {
        $parameters = parameters(test: string());
        $this->expectException(ArgumentCountError::class);
        new Arguments($parameters, []);
    }

    public function testExtraArguments(): void
    {
        $parameters = parameters(test: string());
        $this->expectException(ArgumentCountError::class);
        new Arguments($parameters, [
            'test' => '123',
            'extra' => 'nono',
        ]);
    }

    public function testInvalidArgumentType(): void
    {
        $parameters = parameters(test: string());
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, [
            'test' => 123,
        ]);
    }

    public function testInvalidRegexArgument(): void
    {
        $parameters = parameters(
            id: string()
                ->withRegex(new Regex('/^[0-9]+$/')),
        );
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, [
            'id' => 'abc',
        ]);
    }

    public function testWithMissingArgument(): void
    {
        $name = 'test';
        $parameters = parameters(test: string());
        $arguments = new Arguments($parameters, [
            $name => '123',
        ]);
        $this->expectException(OutOfBoundsException::class);
        $arguments->withPut('notFound', '1234');
    }

    public function testWithArgument(): void
    {
        $name = 'id';
        $value = '123';
        $valueAlt = '321';
        $arguments = new Arguments(
            parameters(
                id: string()
                    ->withRegex(new Regex('/^[0-9]+$/'))
            ),
            [
                $name => $value,
            ]
        );
        $this->assertTrue($arguments->has($name));
        $this->assertSame($value, $arguments->get($name));
        $argumentsWith = $arguments->withPut($name, $valueAlt);
        $this->assertNotSame($arguments, $argumentsWith);
        $this->assertSame($valueAlt, $argumentsWith->get($name));
        $this->expectException(InvalidArgumentException::class);
        $argumentsWith->withPut($name, 'invalid');
    }

    public function testWithArgumentTypeError(): void
    {
        $name = 'id';
        $value = '123';
        $valueAlt = '321';
        $arguments = new Arguments(
            parameters(
                id: string()
            ),
            [
                $name => $value,
            ]
        );
        $this->expectException(TypeError::class);
        $arguments->withPut($name, 123);
    }

    public function testWithArgumentWrongTypeValue(): void
    {
        $arguments = new Arguments(
            parameters(
                id: string()
            ),
            [
                'id' => '123',
            ]
        );
        $this->expectException(TypeError::class);
        $arguments->withPut('id', 123);
    }

    public function testArgumentsRequiredException(): void
    {
        $parameters = parameters(
            id: string()
                ->withRegex(new Regex('/^[0-9]+$/'))
        );
        $this->expectException(ArgumentCountError::class);
        new Arguments($parameters, []);
    }

    public function testParameterOptional(): void
    {
        $parameters = parameters(id: string())
            ->withOptional('opt', string())
            ->withOptional('alt', string())
            ->withRequired('name', string());
        $arguments = new Arguments(
            $parameters,
            [
                'id' => '123',
                'name' => 'ABC',
            ]
        );
        $this->assertTrue($arguments->has('id', 'name'));
        $this->assertFalse($arguments->has('opt', 'alt'));
        $arguments = new Arguments(
            $parameters,
            [
                'id' => '123',
                'opt' => 'someValue',
                'name' => 'ABC',
            ]
        );
        $this->assertTrue($arguments->has('id', 'name', 'opt'));
        $this->assertFalse($arguments->has('alt'));
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[name]');
        $arguments = new Arguments(
            $parameters,
            [
                'id' => '123',
                'name' => new stdClass(),
            ]
        );
    }

    public function testParameterDefault(): void
    {
        $optional = 'name';
        $default = 'a';
        $parameters = parameters()
            ->withOptional(
                $optional,
                string()
                    ->withRegex(new Regex('/^a|b$/'))
                    ->withDefault($default),
            );
        $arguments = new Arguments(
            $parameters,
            []
        );
        $this->assertTrue($arguments->has($optional));
        $this->assertSame($default, $arguments->get($optional));
        $this->assertSame(
            [
                $optional => $default,
            ],
            $arguments->toArray()
        );
    }

    public function testArgumentDefaultOverride(): void
    {
        $optionalString = 'string';
        $optionalStringDefault = 'a';
        $optionalStringValue = 'b';
        $optionalInteger = 'integer';
        $parameters = parameters()
            ->withOptional(
                $optionalString,
                string()
                    ->withRegex(new Regex('/^a|b$/'))
                    ->withDefault($optionalStringDefault),
            );
        $arguments = new Arguments(
            $parameters,
            [
                $optionalString => $optionalStringValue,
            ]
        );
        $this->assertFalse($arguments->has($optionalInteger));
        $this->assertTrue($arguments->has($optionalString));
        $this->assertEquals(
            [
                $optionalString => $optionalStringValue,
            ],
            $arguments->toArray()
        );
    }

    public function testToArrayFill(): void
    {
        $parameters = parameters(
            foo: string(default: 'bar')
        )
            ->withOptional('name', string())
            ->withOptional('id', string());
        $arguments = new Arguments($parameters, []);
        $this->assertFalse($arguments->has('name', 'id'));
        $this->assertEquals([
            'foo' => 'bar',
        ], $arguments->toArray());
        $this->assertSame(
            [
                'name' => null,
                'id' => null,
                'foo' => 'bar',
            ],
            $arguments->toArrayFill(null)
        );
    }

    public function testSkipOptional(): void
    {
        $parameters = parameters()
            ->withOptional('name', string())
            ->withOptional('id', string());
        $expected = [
            'name' => 'foo',
            'id' => 'bar',
        ];
        $arguments = new Arguments($parameters, $expected);
        // $this->assertFalse($arguments->isSkipOptional('name'));
        $this->assertSame($expected, $arguments->toArray());
    }

    public function testCast(): void
    {
        $foo = 'foo';
        $var = true;
        $parameters = parameters(
            foo: boolean(),
        );
        $arguments = new Arguments(
            $parameters,
            [
                $foo => $var,
            ]
        );
        $this->assertSame($var, $arguments->required($foo)->boolean());
        $this->expectException(InvalidArgumentException::class);
        $arguments->optional($foo);
    }

    public function testCastOptional(): void
    {
        $foo = 'foo';
        $var = true;
        $parameters = parameters()->withOptional($foo, boolean());
        $arguments = new Arguments(
            $parameters,
            [
                $foo => $var,
            ]
        );
        $this->assertSame($var, $arguments->optional($foo)?->boolean());
        $arguments = new Arguments($parameters, []);
        $this->assertNull($arguments->optional($foo));
        $this->expectException(InvalidArgumentException::class);
        $arguments->required($foo);
    }

    /**
     * @dataProvider arrayAccessDataProvider
     */
    public function testArrayAccess(
        ArrayParameterInterface $parameter,
        \ArrayAccess $arrayAccess,
        array $array
    ): void {
        $arguments = new Arguments($parameter->parameters(), $arrayAccess);
        $this->assertSame($array, $arguments->toArray());
    }

    public function arrayAccessDataProvider(): array
    {
        $named = [
            'string' => 'test',
            'int' => 1,
            'bool' => false,
        ];
        $parameter = arrayp(
            string: string(),
            int: int(),
            bool: boolean()
        );

        return [
            [
                arrayp(),
                new ArrayAccessDynamic([]),
                [],
            ],
            [
                $parameter,
                new ArrayAccessScoped(...$named),
                $named,
            ],
            [
                $parameter,
                new ArrayAccessDynamic($named),
                $named,
            ],
            [
                $parameter->withRequired(
                    dynamic: string()
                ),
                new ArrayAccessMixed(...$named),
                $named + [
                    'dynamic' => '123abc',
                ],
            ],
        ];
    }

    public function testMinimumOptional(): void
    {
        $parameters = (new Parameters(foo: string()))
            ->withOptional('bar', string());
        new Arguments($parameters, [
            'foo' => '',
        ]);
        $parameters = $parameters->withOptionalMinimum(1);
        new Arguments($parameters, [
            'foo' => '',
            'bar' => '',
        ]);
        $this->expectException(ArgumentCountError::class);
        new Arguments($parameters, [
            'foo' => '',
        ]);
    }

    public function testOptionalNullPass(): void
    {
        $parameters = (new Parameters())->withOptional('foo', string());
        $arguments = [
            'foo' => 'string',
        ];
        new Arguments($parameters, $arguments);
        $arguments = [
            'foo' => null,
        ];
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, $arguments);
    }
}
