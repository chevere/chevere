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
use Chevere\Parameter\Interfaces\IntParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\assertArray;
use function Chevere\Parameter\int;
use function Chevere\Parameter\null;
use function Chevere\Parameter\string;
use function Chevere\Parameter\union;

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
        $int = int();
        $parameter = new ArrayParameter();
        $withRequired = $parameter->withRequired(
            one: $string,
            two: $int
        );
        $this->assertTrue($withRequired->parameters()->has('one', 'two'));
        $this->assertNotSame($parameter, $withRequired);
        $this->assertCount(2, $withRequired->parameters());
        $this->assertInstanceOf(
            StringParameterInterface::class,
            $withRequired->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntParameterInterface::class,
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
                ] + $int->schema(),
            ],
        ], $withRequired->schema());
        $withRequired = $withRequired->withRequired(
            one: $int,
            three: $int
        );
        $this->assertTrue($withRequired->parameters()->has('one', 'two', 'three'));
        $this->assertInstanceOf(
            IntParameterInterface::class,
            $withRequired->parameters()->get('one')
        );
        $this->assertInstanceOf(
            IntParameterInterface::class,
            $withRequired->parameters()->get('three')
        );
    }

    public function testWithOptional(): void
    {
        $string = string();
        $union = union(null(), int());
        $parameter = new ArrayParameter();
        $with = $parameter->withOptional(
            one: $string,
            two: $union
        );
        $assert = assertArray($with, []);
        $this->assertSame([], $assert);
        $expected = [
            'two' => null,
        ];
        $assert = assertArray($with, $expected);
        $this->assertSame($expected, $assert);
        $expected = [
            'two' => 123,
        ];
        $assert = assertArray($with, $expected);
        $this->assertSame($expected, $assert);
        $this->assertTrue($with->parameters()->has('one', 'two'));
        $this->assertNotSame($parameter, $with);
        $this->assertCount(2, $with->parameters());
        $this->assertInstanceOf(
            StringParameterInterface::class,
            $with->parameters()->get('one')
        );
        $this->assertInstanceOf(
            UnionParameterInterface::class,
            $with->parameters()->get('two')
        );
        $with = $with->withOptional(
            one: $union,
            three: $union
        );
        $this->assertTrue($with->parameters()->has('one', 'two', 'three'));
        $this->assertInstanceOf(
            UnionParameterInterface::class,
            $with->parameters()->get('one')
        );
        $this->assertInstanceOf(
            UnionParameterInterface::class,
            $with->parameters()->get('three')
        );
    }

    public function testWithOut(): void
    {
        $string = string();
        $int = int();
        $parameter = (new ArrayParameter())->withRequired(
            one: $string,
            two: $int
        );
        $with = $parameter->without('one');
        $this->assertNotSame($parameter, $with);
        $this->assertCount(1, $with->parameters());
    }

    public function testAssertCompatible(): void
    {
        $string = string();
        $int = int();
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
            one: $int,
        );
        $expectedType = $string::class;
        $failType = $int::class;
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
        $int = int();
        $parameter = (new ArrayParameter())->withRequired(
            a: $string,
            b: $int,
        );
        $this->assertFalse($parameter->isList());
        $this->assertTrue($parameter->isMap());
        $this->assertSame('array#map', $parameter->typeSchema());
        $parameter = (new ArrayParameter())->withRequired($string, $int);
        $this->assertTrue($parameter->isList());
        $this->assertFalse($parameter->isMap());
        $this->assertSame('array#list', $parameter->typeSchema());
    }

    public function testWithOptionalMinimum(): void
    {
        $array = (new ArrayParameter())
            ->withOptional(
                foo: string(),
                bar: string(),
            );
        $arrayWith = $array->withOptionalMinimum(1);
        $this->assertNotSame($array, $arrayWith);
        $this->assertSame(1, $arrayWith->parameters()->optionalMinimum());
    }

    public function testWithMakeOptional(): void
    {
        $array = (new ArrayParameter())
            ->withRequired(
                foo: string(),
                bar: string(),
            );
        $arrayWith = $array->withMakeOptional('foo');
        $this->assertNotSame($array, $arrayWith);
        $this->assertTrue($arrayWith->parameters()->optionalKeys()->contains('foo'));
        $this->assertTrue($arrayWith->parameters()->requiredKeys()->contains('bar'));
    }

    public function testWithMakeRequired(): void
    {
        $array = (new ArrayParameter())
            ->withOptional(
                foo: string(),
                bar: string(),
            );
        $arrayWith = $array->withMakeRequired('bar');
        $this->assertNotSame($array, $arrayWith);
        $this->assertTrue($arrayWith->parameters()->optionalKeys()->contains('foo'));
        $this->assertTrue($arrayWith->parameters()->requiredKeys()->contains('bar'));
    }
}
