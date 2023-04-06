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

use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\assertArray;
use function Chevere\Parameter\assertNamedArgument;
use function Chevere\Parameter\assertUnion;
use function Chevere\Parameter\booleanp;
use function Chevere\Parameter\genericp;
use function Chevere\Parameter\integerp;
use function Chevere\Parameter\nullp;
use function Chevere\Parameter\objectp;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringp;
use function Chevere\Parameter\unionp;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class FunctionsTest extends TestCase
{
    public function testParameters(): void
    {
        $parameters = parameters();
        $this->assertCount(0, $parameters);
        $parameters = parameters(
            foo: stringp()
        );
        $this->assertCount(1, $parameters);
        $this->assertTrue($parameters->isRequired('foo'));
    }

    public function testArguments(): void
    {
        $parameters = parameters(
            foo: stringp()
        );
        $args = [
            'foo' => 'bar',
        ];
        $arguments = arguments($parameters, $args);
        $this->assertSame($args, $arguments->toArray());
    }

    public function testArrayParameter(): void
    {
        $parameter = arrayp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        assertArgument($parameter, []);
    }

    public function testBooleanParameter(): void
    {
        $parameter = booleanp();
        assertArgument($parameter, true);
        $this->assertSame('', $parameter->description());
        $this->assertSame(false, $parameter->default());
        $parameter = booleanp(
            description: 'name',
            default: true
        );
        $this->assertSame('name', $parameter->description());
        $this->assertSame(true, $parameter->default());
        $this->expectException(TypeError::class);
        assertArgument($parameter, null);
    }

    public function testNullParameter(): void
    {
        $parameter = nullp();
        assertArgument($parameter, null);
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->expectException(TypeError::class);
        assertArgument($parameter, 1);
    }

    public function testIntegerParameter(): void
    {
        $parameter = integerp();
        assertArgument($parameter, 1);
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $parameter = integerp(
            default: 10
        );
        $this->assertSame(10, $parameter->default());
        $this->expectException(TypeError::class);
        assertArgument($parameter, '');
    }

    public function testFunctionObjectParameter(): void
    {
        $parameter = objectp(stdClass::class);
        assertArgument($parameter, new stdClass());
        $this->assertSame('', $parameter->description());
        $this->assertSame(stdClass::class, $parameter->className());
        $parameter = objectp(stdClass::class, 'foo');
        $this->assertSame('foo', $parameter->description());
        $this->expectException(InvalidArgumentException::class);
        assertArgument($parameter, parameters());
    }

    public function testFunctionStringParameter(): void
    {
        $description = 'some description';
        $default = 'abcd';
        $regex = '/^[a-z]+$/';
        $parameter = stringp(
            description: $description,
            default: $default,
            regex: $regex,
        );
        assertArgument($parameter, $default);
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame($regex, $parameter->regex()->__toString());
        $this->expectException(TypeError::class);
        assertArgument($parameter, 123);
    }

    public function testFunctionArrayParameter(): void
    {
        $parameter = arrayp(
            wea: arrayp(
                one: stringp(),
                two: integerp()
            )
        );
        assertArray($parameter, [
            'wea' => [
                'one' => 'foo',
                'two' => 123,
            ],
        ]);
        $this->assertCount(1, $parameter->parameters());
        $this->expectException(TypeError::class);
        assertArgument($parameter, 1);
    }

    public function testFunctionAssertArgument(): void
    {
        assertNamedArgument('test', integerp(), 123);
        $this->expectException(InvalidArgumentException::class);
        assertNamedArgument('fail', stringp(), 13.13);
    }

    public function testFunctionGenericParameter(): void
    {
        $parameter = genericp(
            V: stringp()
        );
        $this->assertSame('', $parameter->description());
        $parameter = genericp(
            K: stringp(),
            V: stringp(),
            description: 'foo'
        );
        $this->assertSame('foo', $parameter->description());
    }

    public function testFunctionUnionParameter(): void
    {
        $parameter = unionp(
            integerp(),
            stringp(),
        );
        assertUnion($parameter, 'foo');
        assertUnion($parameter, 123);
        $this->expectException(InvalidArgumentException::class);
        assertUnion($parameter, []);
    }

    public function testAssertArrayExtraArguments(): void
    {
        $parameter = arrayp(
            OK: stringp(),
        );
        $this->expectException(ArgumentCountError::class);
        assertArray($parameter, [
            'OK' => 'abc',
            'ERROR' => 123,
        ]);
    }

    public function testAssertArrayConflictType(): void
    {
        $parameter = arrayp(
            OK: stringp(),
        );
        $this->expectException(InvalidArgumentException::class);
        assertArray($parameter, [
            'OK' => 123,
        ]);
    }

    public function testAssertArrayConflictNull(): void
    {
        $parameter = arrayp(
            OK: stringp(),
        );
        $this->expectException(InvalidArgumentException::class);
        assertArray($parameter, [
            'OK' => null,
        ]);
    }
}
