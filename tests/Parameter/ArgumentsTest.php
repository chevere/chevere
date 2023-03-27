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
use function Chevere\Parameter\arrayp;
use Chevere\Parameter\BooleanParameter;
use Chevere\Parameter\FloatParameter;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\ObjectParameter;
use function Chevere\Parameter\parameters;
use Chevere\Parameter\Parameters;
use function Chevere\Parameter\stringp;
use Chevere\Parameter\StringParameter;
use Chevere\Regex\Regex;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ArgumentsTest extends TestCase
{
    public function testInvalidArgument(): void
    {
        $parameters = new Parameters(test: new StringParameter());
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
        $parameters = new Parameters(
            id: new IntegerParameter(),
            name: new StringParameter()
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
        $parameters = new Parameters(test: new StringParameter());
        $this->expectException(ArgumentCountError::class);
        new Arguments($parameters, []);
    }

    public function testExtraArguments(): void
    {
        $parameters = new Parameters(test: new StringParameter());
        $this->expectException(ArgumentCountError::class);
        new Arguments($parameters, [
            'test' => '123',
            'extra' => 'nono',
        ]);
    }

    public function testInvalidArgumentType(): void
    {
        $parameters = new Parameters(test: new StringParameter());
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, [
            'test' => 123,
        ]);
    }

    public function testInvalidRegexArgument(): void
    {
        $parameters = new Parameters(
            id: (new StringParameter())
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
        $parameters = new Parameters(test: new StringParameter());
        $arguments = new Arguments($parameters, [
            $name => '123',
        ]);
        $this->expectException(OutOfBoundsException::class);
        $arguments->withPut(notFound: '1234');
    }

    public function testWithArgument(): void
    {
        $name = 'id';
        $value = '123';
        $valueAlt = '321';
        $arguments = new Arguments(
            new Parameters(
                id: (new StringParameter())
                    ->withRegex(new Regex('/^[0-9]+$/'))
            ),
            [
                $name => $value,
            ]
        );
        $this->assertTrue($arguments->has($name));
        $this->assertSame($value, $arguments->get($name));
        $argumentsWith = $arguments->withPut(...[
            $name => $valueAlt,
        ]);
        $this->assertNotSame($arguments, $argumentsWith);
        $this->assertSame($valueAlt, $argumentsWith->get($name));
        $this->expectException(InvalidArgumentException::class);
        $argumentsWith->withPut(...[
            $name => 'invalid',
        ]);
    }

    public function testWithArgumentTypeError(): void
    {
        $name = 'id';
        $value = '123';
        $valueAlt = '321';
        $arguments = new Arguments(
            new Parameters(
                id: new StringParameter()
            ),
            [
                $name => $value,
            ]
        );
        $this->expectException(TypeError::class);
        $arguments->withPut(...[
            $name => 123,
        ]);
    }

    public function testWithArgumentWrongTypeValue(): void
    {
        $arguments = new Arguments(
            new Parameters(
                id: new StringParameter()
            ),
            [
                'id' => '123',
            ]
        );
        $this->expectException(TypeError::class);
        $arguments->withPut(id: 123);
    }

    public function testArgumentsRequiredException(): void
    {
        $parameters = new Parameters(
            id: (new StringParameter())
                ->withRegex(new Regex('/^[0-9]+$/'))
        );
        $this->expectException(ArgumentCountError::class);
        new Arguments($parameters, []);
    }

    public function testParameterOptional(): void
    {
        $required = 'id';
        $optional = 'name';
        $arguments = new Arguments(
            (new Parameters(id: new StringParameter()))
                ->withAddedOptional(
                    name: new StringParameter()
                ),
            [
                $required => '123',
            ]
        );
        $this->assertTrue($arguments->has($optional));
    }

    public function testParameterDefault(): void
    {
        $required = 'id';
        $optional = 'name';
        $requiredValue = '123';
        $optionalDefault = 'a';
        $parameters = (new Parameters(id: new StringParameter()))
            ->withAddedOptional(
                ...[
                    $optional => (new StringParameter())
                        ->withRegex(new Regex('/^a|b$/'))
                        ->withDefault($optionalDefault),
                ]
            );
        $arguments = new Arguments(
            $parameters,
            [
                $required => $requiredValue,
            ]
        );
        $this->assertTrue($arguments->has($optional));
        $this->assertSame($optionalDefault, $arguments->get($optional));
        $this->assertSame(
            [
                $required => $requiredValue,
                $optional => $optionalDefault,
            ],
            $arguments->toArray()
        );
    }

    public function testArgumentDefaultOverride(): void
    {
        $required = 'id';
        $optionalName = 'name';
        $requiredValue = '123';
        $optionalDefault = 'a';
        $optionalNameValue = 'b';
        $optionalObject = 'object';
        $parameters = (new Parameters(id: new StringParameter()))
            ->withAddedOptional(
                ...[
                    $optionalName => (new StringParameter())
                        ->withRegex(new Regex('/^a|b$/'))
                        ->withDefault($optionalDefault),
                    $optionalObject => new ObjectParameter(),
                ]
            );
        $argumentsWithAllValues = new Arguments(
            $parameters,
            [
                $required => $requiredValue,
                $optionalName => $optionalNameValue,
            ]
        );
        $this->assertEquals(
            [
                $required => $requiredValue,
                $optionalName => $optionalNameValue,
                $optionalObject => new stdClass(),
            ],
            $argumentsWithAllValues->toArray()
        );
    }

    public function testGetBoolean(): void
    {
        $name = 'test';
        $var = true;
        $arguments = new Arguments(
            new Parameters(test: new BooleanParameter()),
            [
                $name => $var,
            ]
        );
        $this->assertSame($var, $arguments->getBoolean($name));
        $this->expectException(\TypeError::class);
        $arguments->getString($name);
    }

    public function testGetString(): void
    {
        $name = 'test';
        $var = 'string';
        $arguments = new Arguments(
            new Parameters(test: new StringParameter()),
            [
                $name => $var,
            ]
        );
        $this->assertSame($var, $arguments->getString($name));
        $this->expectException(\TypeError::class);
        $arguments->getBoolean($name);
    }

    public function testGetInteger(): void
    {
        $name = 'test';
        $var = 1234;
        $arguments = new Arguments(
            new Parameters(test: new IntegerParameter()),
            [
                $name => $var,
            ]
        );
        $this->assertSame($var, $arguments->getInteger($name));
        $this->expectException(\TypeError::class);
        $arguments->getArray($name);
    }

    public function testGetFloat(): void
    {
        $name = 'test';
        $var = 12.34;
        $arguments = new Arguments(
            new Parameters(test: new FloatParameter($name)),
            [
                $name => $var,
            ]
        );
        $this->assertSame($var, $arguments->getFloat($name));
        $this->expectException(\TypeError::class);
        $arguments->getArray($name);
    }

    public function testGetArray(): void
    {
        $name = 'test';
        $var = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
        $parameters = parameters(
            test: arrayp(
                a: stringp('/^A$/'),
                b: stringp('/^B$/'),
                c: stringp('/^C$/'),
            )
        );
        $arguments = new Arguments(
            $parameters,
            [
                $name => $var,
            ]
        );
        $this->assertSame($var, $arguments->getArray($name));
        $this->expectException(\TypeError::class);
        $arguments->getInteger($name);
    }
}
