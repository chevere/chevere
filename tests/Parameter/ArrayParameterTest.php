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

use Chevere\Parameter\ArrayParameter;
use function Chevere\Parameter\integerp;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use function Chevere\Parameter\stringp;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ArrayParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new ArrayParameter();
        $this->assertSame(null, $parameter->default());
        $this->assertCount(0, $parameter->parameters());
        $default = ['test', 1];
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'array',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
    }

    public function testWithRequired(): void
    {
        $string = stringp();
        $integer = integerp();
        $parameter = new ArrayParameter();
        $parameterWith = $parameter->withRequired(
            one: $string,
            two: $integer
        );
        $this->assertTrue($parameterWith->parameters()->has('one', 'two'));
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(2, $parameterWith->parameters());
        $this->assertInstanceOf(
            StringParameterInterface::class,
            $parameterWith->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $parameterWith->parameters()->get('two')
        );
        $parameterWith = $parameterWith->withRequired(
            one: $integer,
            three: $integer
        );
        $this->assertTrue($parameterWith->parameters()->has('one', 'two', 'three'));
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $parameterWith->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $parameterWith->parameters()->get('three')
        );
    }

    public function testWithOptional(): void
    {
        $string = stringp();
        $integer = integerp();
        $parameter = new ArrayParameter();
        $parameterWith = $parameter->withOptional(
            one: $string,
            two: $integer
        );
        $this->assertTrue($parameterWith->parameters()->has('one', 'two'));
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(2, $parameterWith->parameters());
        $this->assertInstanceOf(
            StringParameterInterface::class,
            $parameterWith->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $parameterWith->parameters()->get('two')
        );
        $parameterWith = $parameterWith->withOptional(
            one: $integer,
            three: $integer
        );
        $this->assertTrue($parameterWith->parameters()->has('one', 'two', 'three'));
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $parameterWith->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $parameterWith->parameters()->get('three')
        );
    }

    public function testWithOut(): void
    {
        $string = stringp();
        $integer = integerp();
        $parameter = (new ArrayParameter())->withRequired(
            one: $string,
            two: $integer
        );
        $parameterWith = $parameter->without('one');
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(1, $parameterWith->parameters());
    }

    public function testAssertCompatibleConflict(): void
    {
        $string = stringp();
        $integer = integerp();
        $parameter = (new ArrayParameter())->withRequired(
            one: $string,
        );
        $altString = stringp();
        $compatible = (new ArrayParameter())->withRequired(
            one: $altString,
        );
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $notCompatible = (new ArrayParameter())->withRequired(
            one: $integer,
        );
        $expectedType = $string::class;
        $failType = $integer::class;
        $this->expectExceptionMessage(
            <<<STRING
            Parameter one of type {$expectedType} is not compatible with type {$failType}
            STRING
        );
        $this->expectException(InvalidArgumentException::class);
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleMissingKey(): void
    {
        $string = stringp();
        $parameter = (new ArrayParameter())->withRequired(
            one: $string,
        );
        $notCompatible = (new ArrayParameter())->withRequired(
            two: $string,
        );
        $this->expectException(OutOfBoundsException::class);
        $parameter->assertCompatible($notCompatible);
    }
}
