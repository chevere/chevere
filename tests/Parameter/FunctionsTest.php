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

use function Chevere\Parameter\arrayParameter;
use function Chevere\Parameter\booleanParameter;
use function Chevere\Parameter\floatParameter;
use function Chevere\Parameter\integerParameter;
use function Chevere\Parameter\objectParameter;
use function Chevere\Parameter\parameters;
use function Chevere\Parameter\stringParameter;
use PHPUnit\Framework\TestCase;
use stdClass;

final class FunctionsTest extends TestCase
{
    public function testFunctionParameters(): void
    {
        $parameters = parameters();
        $this->assertCount(0, $parameters);
        $parameters = parameters(
            foo: stringParameter()
        );
        $this->assertCount(1, $parameters);
        $this->assertTrue($parameters->isRequired('foo'));
    }

    public function testArrayParameter(): void
    {
        $parameter = arrayParameter();
        $this->assertSame('', $parameter->description());
        $this->assertSame([], $parameter->default());
    }

    public function testBooleanParameter(): void
    {
        $parameter = booleanParameter();
        $this->assertSame('', $parameter->description());
        $this->assertSame(false, $parameter->default());
        $parameter = booleanParameter(
            description: 'name',
            default: true
        );
        $this->assertSame('name', $parameter->description());
        $this->assertSame(true, $parameter->default());
    }

    public function testFloatParameter(): void
    {
        $parameter = floatParameter();
        $this->assertSame('', $parameter->description());
        $this->assertSame(0.0, $parameter->default());
        $parameter = floatParameter(
            description: 'name',
            default: 5.5
        );
        $this->assertSame('name', $parameter->description());
        $this->assertSame(5.5, $parameter->default());
    }

    public function testIntegerParameter(): void
    {
        $parameter = integerParameter();
        $this->assertSame('', $parameter->description());
        $this->assertSame(0, $parameter->default());
        $parameter = integerParameter(
            default: 10
        );
        $this->assertSame(10, $parameter->default());
    }

    public function testFunctionObjectParameter(): void
    {
        $parameter = objectParameter(stdClass::class);
        $this->assertSame('', $parameter->description());
        $this->assertSame(stdClass::class, $parameter->className());
        $parameter = objectParameter(stdClass::class, 'foo');
        $this->assertSame('foo', $parameter->description());
    }

    public function testFunctionStringParameter(): void
    {
        $description = 'some description';
        $default = 'abcd';
        $regex = '/^[a-z]+$/';
        $parameter = stringParameter(
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
        $description = 'ola k ase';
        $parameter = arrayParameter(
            wea: arrayParameter(
                one: stringParameter(),
                two: integerParameter()
            )
        );
        $this->assertCount(1, $parameter->parameters());
    }
}
