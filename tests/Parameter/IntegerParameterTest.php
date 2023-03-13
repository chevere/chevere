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
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\Parameters;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use OverflowException;
use PHPUnit\Framework\TestCase;

final class IntegerParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new IntegerParameter();
        $this->assertSame(0, $parameter->default());
        $this->assertSame(PHP_INT_MIN, $parameter->minimum());
        $this->assertSame(PHP_INT_MAX, $parameter->maximum());
        $this->assertSame([], $parameter->accept());
        $default = 1234;
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'integer',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
    }

    public function testWithAccept(): void
    {
        $accept = [1, 2, 3];
        $parameter = new IntegerParameter();
        $withValue = $parameter->withAccept(...$accept);
        $this->assertNotSame($parameter, $withValue);
        $this->assertSame($accept, $withValue->accept());
    }

    public function testWithAcceptOnArguments(): void
    {
        $accept = [1, 2, 3];
        $expect = [
            'test' => 1,
        ];
        $parameter = (new IntegerParameter())->withAccept(...$accept);
        $parameters = new Parameters(test: $parameter);
        $arguments = new Arguments($parameters, $expect);
        $this->assertSame($expect, $arguments->toArray());
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, [
            'test' => 0,
        ]);
    }

    public function testWithMinimum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWithMinimum = $parameter->withMinimum(1);
        $this->assertNotSame($parameter, $parameterWithMinimum);
        $this->assertSame(1, $parameterWithMinimum->minimum());
        $parameterWithValue = $parameter->withAccept(1);
        $this->assertSame(null, $parameterWithValue->maximum());
        $this->assertSame(null, $parameterWithValue->minimum());
        $this->expectException(OverflowException::class);
        $parameterWithValue->withMinimum(0);
    }

    public function testWithMinimumOnArguments(): void
    {
        $expect = [
            'test' => 1,
        ];
        $parameter = (new IntegerParameter())->withMinimum(1);
        $parameters = new Parameters(test: $parameter);
        $arguments = new Arguments($parameters, $expect);
        $this->assertSame($expect, $arguments->toArray());
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, [
            'test' => -1,
        ]);
    }

    public function testWithMaximum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWithMaximum = $parameter->withMaximum(1);
        $this->assertNotSame($parameter, $parameterWithMaximum);
        $this->assertSame(1, $parameterWithMaximum->maximum());
        $parameterWithValue = $parameter->withAccept(1);
        $this->assertSame(null, $parameterWithValue->maximum());
        $this->assertSame(null, $parameterWithValue->minimum());
        $this->expectException(OverflowException::class);
        $parameterWithValue->withMaximum(0);
    }

    public function testWithMaximumOnArguments(): void
    {
        $expect = [
            'test' => 1,
        ];
        $parameter = (new IntegerParameter())->withMaximum(1);
        $parameters = new Parameters(test: $parameter);
        $arguments = new Arguments($parameters, $expect);
        $this->assertSame($expect, $arguments->toArray());
        $this->expectException(InvalidArgumentException::class);
        new Arguments($parameters, [
            'test' => 2,
        ]);
    }

    public function testWithMinimumMaximum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWith = $parameter
            ->withMinimum(1)
            ->withMaximum(2);
        $this->expectException(InvalidArgumentException::class);
        $parameterWith->withMinimum(2);
    }

    public function testWithMaximumMinimum(): void
    {
        $parameter = new IntegerParameter();
        $parameterWith = $parameter
            ->withMinimum(1)
            ->withMaximum(2);
        $this->expectException(InvalidArgumentException::class);
        $parameterWith->withMaximum(1);
    }

    public function testAssertCompatibleMinimum(): void
    {
        $value = 1;
        $parameter = (new IntegerParameter())->withMinimum($value);
        $compatible = (new IntegerParameter())->withMinimum($value);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $provided = $value * 2;
        $notCompatible = (new IntegerParameter())->withMinimum($provided);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Expected minimum value {$value}, provided {$provided}
            STRING
        );
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleMaximum(): void
    {
        $value = 1;
        $compatible = (new IntegerParameter())->withMaximum($value);
        $parameter = (new IntegerParameter())->withMaximum($value);
        $compatible = (new IntegerParameter())->withMaximum($value);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $provided = $value * 2;
        $notCompatible = (new IntegerParameter())->withMaximum($provided);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Expected maximum value {$value}, provided {$provided}
            STRING
        );
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleAccept(): void
    {
        $parameter = (new IntegerParameter())->withAccept(1, 2, 3);
        $compatible = (new IntegerParameter())->withAccept(3, 2, 1);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $notCompatible = (new IntegerParameter())->withAccept(0);
        $this->expectException(InvalidArgumentException::class);
        $this->getExpectedExceptionMessage('[' . implode(', ', $parameter->accept()) . ']');
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleAcceptMinimum(): void
    {
        $parameter = (new IntegerParameter())->withAccept(1, 2, 3);
        $notCompatible = (new IntegerParameter())->withMinimum(0);
        $this->expectException(InvalidArgumentException::class);
        $this->getExpectedExceptionMessage('value null');
        $parameter->assertCompatible($notCompatible);
    }
}
