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
use function Chevere\Parameter\assertNamedArgument;
use function Chevere\Parameter\assertUnion;
use function Chevere\Parameter\booleanp;
use function Chevere\Parameter\floatp;
use function Chevere\Parameter\genericp;
use function Chevere\Parameter\integerp;
use function Chevere\Parameter\nullp;
use function Chevere\Parameter\objectp;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringp;
use function Chevere\Parameter\unionp;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

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
        $this->assertSame([], $parameter->default());
    }

    public function testBooleanParameter(): void
    {
        $parameter = booleanp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(false, $parameter->default());
        $parameter = booleanp(
            description: 'name',
            default: true
        );
        $this->assertSame('name', $parameter->description());
        $this->assertSame(true, $parameter->default());
    }

    public function testNullParameter(): void
    {
        $parameter = nullp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $parameter = booleanp('name');
        $this->assertSame('name', $parameter->description());
    }

    public function testFloatParameter(): void
    {
        $parameter = floatp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(0.0, $parameter->default());
        $parameter = floatp(
            description: 'name',
            default: 5.5
        );
        $this->assertSame('name', $parameter->description());
        $this->assertSame(5.5, $parameter->default());
    }

    public function testIntegerParameter(): void
    {
        $parameter = integerp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(0, $parameter->default());
        $parameter = integerp(
            default: 10
        );
        $this->assertSame(10, $parameter->default());
    }

    public function testFunctionObjectParameter(): void
    {
        $parameter = objectp(stdClass::class);
        $this->assertSame('', $parameter->description());
        $this->assertSame(stdClass::class, $parameter->className());
        $parameter = objectp(stdClass::class, 'foo');
        $this->assertSame('foo', $parameter->description());
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
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame($regex, $parameter->regex()->__toString());
    }

    public function testFunctionArrayParameter(): void
    {
        $parameter = arrayp(
            wea: arrayp(
                one: stringp(),
                two: integerp()
            )
        );
        $this->assertCount(1, $parameter->parameters());
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
            K: stringp(),
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
}
