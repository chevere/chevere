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
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\integer;
use function Chevere\Parameter\string;

final class ArrayParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new ArrayParameter();
        $this->assertSame(null, $parameter->default());
        $this->assertCount(0, $parameter->parameters());
        $this->assertSame([
            'type' => 'array#map',
            'description' => '',
            'default' => null,
            'parameters' => [],
        ], $parameter->schema());
        $default = ['test', 1];
        $withDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'array',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $withDefault
        );
        $this->assertSame([
            'type' => 'array#map',
            'description' => '',
            'default' => $default,
            'parameters' => [],
        ], $withDefault->schema());
    }

    public function testWithRequired(): void
    {
        $string = string();
        $integer = integer();
        $parameter = new ArrayParameter();
        $withRequired = $parameter->withRequired(
            one: $string,
            two: $integer
        );
        $this->assertTrue($withRequired->parameters()->has('one', 'two'));
        $this->assertNotSame($parameter, $withRequired);
        $this->assertCount(2, $withRequired->parameters());
        $this->assertInstanceOf(
            StringParameterInterface::class,
            $withRequired->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $withRequired->parameters()->get('two')
        );
        $this->assertSame([
            'type' => 'array#map',
            'description' => '',
            'default' => null,
            'parameters' => [
                'one' => [
                    'required' => true,
                ] + $string->schema(),
                'two' => [
                    'required' => true,
                ] + $integer->schema(),
            ],
        ], $withRequired->schema());
        $withRequired = $withRequired->withRequired(
            one: $integer,
            three: $integer
        );
        $this->assertTrue($withRequired->parameters()->has('one', 'two', 'three'));
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $withRequired->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $withRequired->parameters()->get('three')
        );
    }

    public function testWithOptional(): void
    {
        $string = string();
        $integer = integer();
        $parameter = new ArrayParameter();
        $with = $parameter->withOptional(
            one: $string,
            two: $integer
        );
        $this->assertTrue($with->parameters()->has('one', 'two'));
        $this->assertNotSame($parameter, $with);
        $this->assertCount(2, $with->parameters());
        $this->assertInstanceOf(
            StringParameterInterface::class,
            $with->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $with->parameters()->get('two')
        );
        $with = $with->withOptional(
            one: $integer,
            three: $integer
        );
        $this->assertTrue($with->parameters()->has('one', 'two', 'three'));
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $with->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntegerParameterInterface::class,
            $with->parameters()->get('three')
        );
    }

    public function testWithOut(): void
    {
        $string = string();
        $integer = integer();
        $parameter = (new ArrayParameter())->withRequired(
            one: $string,
            two: $integer
        );
        $with = $parameter->without('one');
        $this->assertNotSame($parameter, $with);
        $this->assertCount(1, $with->parameters());
    }

    public function testAssertCompatible(): void
    {
        $string = string();
        $integer = integer();
        $parameter = (new ArrayParameter())->withRequired(
            one: $string,
        );
        $altString = string();
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
        $string = string();
        $parameter = (new ArrayParameter())->withRequired(
            one: $string,
        );
        $notCompatible = (new ArrayParameter())->withRequired(
            two: $string,
        );
        $this->expectException(OutOfBoundsException::class);
        $parameter->assertCompatible($notCompatible);
    }

    public function testIsList(): void
    {
        $string = string();
        $integer = integer();
        $parameter = (new ArrayParameter())->withRequired(
            a: $string,
            b: $integer,
        );
        $this->assertFalse($parameter->isList());
        $this->assertTrue($parameter->isMap());
        $this->assertSame('array#map', $parameter->typeSchema());
        $parameter = (new ArrayParameter())->withRequired($string, $integer);
        $this->assertTrue($parameter->isList());
        $this->assertFalse($parameter->isMap());
        $this->assertSame('array#list', $parameter->typeSchema());
    }
}
