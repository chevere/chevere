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

use BadMethodCallException;
use Chevere\Parameter\ArrayParameter;
use Chevere\Parameter\BoolParameter;
use Chevere\Parameter\FloatParameter;
use Chevere\Parameter\GenericParameter;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\IntParameter;
use Chevere\Parameter\NullParameter;
use Chevere\Parameter\ObjectParameter;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Parameter\UnionParameter;
use InvalidArgumentException;
use OutOfBoundsException;
use OverflowException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\int;
use function Chevere\Parameter\string;

final class ParametersTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $name = 'name';
        $parameters = new Parameters();
        $this->assertCount(0, $parameters);
        $this->assertCount(0, $parameters->optionalKeys());
        $this->assertCount(0, $parameters->requiredKeys());
        $this->assertFalse($parameters->has($name));
        $this->expectException(OutOfBoundsException::class);
        $parameters->get($name);
    }

    public function testAssertEmpty(): void
    {
        $name = 'name';
        $parameters = new Parameters();
        $this->expectException(OutOfBoundsException::class);
        $parameters->assertHas($name);
    }

    public function testConstruct(): void
    {
        $name = 'name';
        $parameter = new StringParameter();
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertCount(1, $parameters);
        $this->assertCount(0, $parameters->optionalKeys());
        $this->assertCount(1, $parameters->requiredKeys());
        $parameters->assertHas($name);
        $this->assertTrue($parameters->has($name));
        $this->assertTrue($parameters->requiredKeys()->contains($name));
        $this->assertSame($parameter, $parameters->get($name));
        $this->expectException(OverflowException::class);
        $parameters->withRequired(
            $name,
            $parameter,
        );
    }

    public function testConstructPositional(): void
    {
        $foo = string();
        $bar = int();
        $parameters = new Parameters($foo, $bar);
        $this->assertCount(2, $parameters);
        $this->assertSame($foo, $parameters->get('0'));
        $this->assertSame($bar, $parameters->get('1'));
    }

    public function testRequiredCasting(): void
    {
        $parameter = string();
        $parameters = new Parameters(foo: $parameter);
        $this->assertSame($parameter, $parameters->required('foo')->string());
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter `foo` is required');
        $parameters->optional('foo');
    }

    public function testOptionalCasting(): void
    {
        $parameter = string();
        $parameters = (new Parameters())
            ->withOptional('foo', $parameter);
        $this->assertSame($parameter, $parameters->optional('foo')->string());
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter `foo` is optional');
        $parameters->required('foo');
    }

    public function testWithRequiredOverflow(): void
    {
        $name = 'name';
        $parameter = new StringParameter();
        $parameters = new Parameters(
            ...[
                $name => $parameter,
            ]
        );
        $this->assertCount(1, $parameters);
        $this->assertCount(0, $parameters->optionalKeys());
        $this->assertCount(1, $parameters->requiredKeys());
        $parameters->assertHas($name);
        $this->assertTrue($parameters->has($name));
        $this->assertTrue($parameters->requiredKeys()->contains($name));
        $this->assertSame($parameter, $parameters->get($name));
        $parametersWith = $parameters->withRequired('test', $parameter);
        $this->assertNotSame($parameters, $parametersWith);
        $this->expectException(OverflowException::class);
        $parameters->withRequired(
            $name,
            $parameter,
        );
    }

    public function testWithout(): void
    {
        $parameters = (new Parameters())
            ->withRequired('a', string())
            ->withRequired('b', string())
            ->withRequired('c', string())
            ->withOptional('x', string())
            ->withOptional('y', string())
            ->withOptional('z', string());
        $parametersWith = $parameters->without('a', 'y');
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(4, $parametersWith);
        $this->assertSame(['b', 'c'], $parametersWith->requiredKeys()->toArray());
        $this->assertSame(['x', 'z'], $parametersWith->optionalKeys()->toArray());
    }

    public function testWithRequiredOptional(): void
    {
        $name = 'name';
        $parameter = new StringParameter();
        $parameters = new Parameters();
        $parametersWith = $parameters->withOptional($name, $parameter);
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(1, $parametersWith);
        $this->assertCount(1, $parametersWith->optionalKeys());
        $this->assertCount(0, $parametersWith->requiredKeys());
        $this->assertTrue($parametersWith->has($name));
        $this->assertTrue($parametersWith->optionalKeys()->contains($name));
        $this->assertFalse($parametersWith->requiredKeys()->contains($name));
        $this->assertSame($parameter, $parametersWith->get($name));
        $this->expectException(OverflowException::class);
        $parametersWith->withOptional($name, $parameter);
    }

    public function dataProviderCast(): array
    {
        return [
            [new StringParameter(), 'string'],
            [new IntParameter(), 'int'],
            [new FloatParameter(), 'float'],
            [new BoolParameter(), 'bool'],
            [new ArrayParameter(), 'array'],
            [new ObjectParameter(), 'object'],
            [new NullParameter(), 'null', 'int'],
        ];
    }

    /**
     * @dataProvider dataProviderCast
     */
    public function testGetCast(
        ParameterInterface $parameter,
        string $type,
        string $error = 'null'
    ): void {
        $name = 'test';
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame(
            $parameter,
            $parameters->required($name)->{$type}()
        );
        $this->expectException(\TypeError::class);
        $parameters->required($name)->{$error}();
    }

    public function testGetUnion(): void
    {
        $name = 'test';
        $type1 = new StringParameter();
        $type2 = new IntParameter();
        $parameters = new Parameters($type1, $type2);
        $parameter = new UnionParameter($parameters);
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame(
            $parameter,
            $parameters->required($name)->union()
        );
        $this->expectException(\TypeError::class);
        $parameters->required($name)->null();
    }

    public function testGetGeneric(): void
    {
        $name = 'test';
        $parameter = new GenericParameter(
            value: string(),
            key: int(),
        );
        $parameters = new Parameters(...[
            $name => $parameter,
        ]);
        $this->assertSame(
            $parameter,
            $parameters->required($name)->generic()
        );
        $this->expectException(\TypeError::class);
        $parameters->required($name)->null();
    }

    public function testWithOptionalMinimum(): void
    {
        $parameters = (new Parameters())->withOptional('a', string());
        $parametersWith = $parameters->withOptionalMinimum(1);
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertSame(1, $parametersWith->optionalMinimum());
    }

    public function testWithOptionalMinimumBadMethodCall(): void
    {
        $parameters = new Parameters();
        $this->expectException(BadMethodCallException::class);
        $parameters->withOptionalMinimum(1);
    }

    public function testWithOptionalMinimumInvalidArgument(): void
    {
        $parameters = (new Parameters())->withOptional('foo', string());
        $this->expectException(InvalidArgumentException::class);
        $parameters->withOptionalMinimum(2);
    }

    public function testWithOptionalMinimumInvalidArgumentNumber(): void
    {
        $parameters = (new Parameters())->withOptional('foo', string());
        $this->expectException(InvalidArgumentException::class);
        $parameters->withOptionalMinimum(-1);
    }

    public function testWithOptionalMinimumWithout(): void
    {
        $parameters = (new Parameters())
            ->withOptional('foo', string())
            ->withOptional('bar', string());
        $parametersWith = $parameters->withOptionalMinimum(1);
        $parametersWith = $parametersWith->without('foo');
        $parametersWith = $parametersWith->withOptionalMinimum(0);
        $this->expectNotToPerformAssertions();
        $parametersWith->without('bar');
    }

    public function testWithOptionalMinimumWithoutInvalidArgument(): void
    {
        $parameters = (new Parameters())->withOptional('foo', string());
        $parametersWith = $parameters->withOptionalMinimum(1);
        $this->expectException(InvalidArgumentException::class);
        $parametersWith->without('foo');
    }

    public function testWithMakeOptional(): void
    {
        $parameters = new Parameters(
            foo: string(),
            bar: int()
        );
        $parametersWith = $parameters->withMakeOptional('foo');
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(2, $parametersWith);
        $this->assertCount(1, $parametersWith->optionalKeys());
        $this->assertCount(1, $parametersWith->requiredKeys());
        $this->assertTrue($parametersWith->optionalKeys()->contains('foo'));
        $this->assertTrue($parametersWith->requiredKeys()->contains('bar'));
        $this->expectException(InvalidArgumentException::class);
        $parametersWith->withMakeOptional('foo');
    }

    public function testWithMakeRequired(): void
    {
        $parameters = (new Parameters())
            ->withOptional('foo', string())
            ->withOptional('bar', int());
        $parametersWith = $parameters->withMakeRequired('bar');
        $this->assertNotSame($parameters, $parametersWith);
        $this->assertCount(2, $parametersWith);
        $this->assertCount(1, $parametersWith->optionalKeys());
        $this->assertCount(1, $parametersWith->requiredKeys());
        $this->assertTrue($parametersWith->optionalKeys()->contains('foo'));
        $this->assertTrue($parametersWith->requiredKeys()->contains('bar'));
        $this->expectException(InvalidArgumentException::class);
        $parametersWith->withMakeRequired('bar');
    }
}
