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
use Chevere\Parameter\IntParameter;
use Chevere\Parameter\Parameters;
use InvalidArgumentException;
use OverflowException;
use PHPUnit\Framework\TestCase;

final class IntParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new IntParameter();
        $this->assertSame(null, $parameter->default());
        $this->assertSame(null, $parameter->min());
        $this->assertSame(null, $parameter->max());
        $this->assertSame([], $parameter->accept());
        $default = 1234;
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'int',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
        $this->assertSame([
            'type' => 'int',
            'description' => '',
            'default' => $default,
            'minimum' => null,
            'maximum' => null,
            'accept' => [],
        ], $parameterWithDefault->schema());
    }

    public function testWithAccept(): void
    {
        $accept = [3, 2, 1];
        $sorted = [1, 2, 3];
        $parameter = new IntParameter();
        $withAccept = $parameter->withAccept(...$accept);
        $this->assertNotSame($parameter, $withAccept);
        $this->assertSame($sorted, $withAccept->accept());
        $this->assertSame([
            'type' => 'int',
            'description' => '',
            'default' => null,
            'minimum' => null,
            'maximum' => null,
            'accept' => $sorted,
        ], $withAccept->schema());
    }

    public function testWithAcceptOnArguments(): void
    {
        $accept = [1, 2, 3];
        $expect = [
            'test' => 1,
        ];
        $parameter = (new IntParameter())->withAccept(...$accept);
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
        $parameter = new IntParameter();
        $parameterWithMinimum = $parameter->withMin(1);
        $this->assertNotSame($parameter, $parameterWithMinimum);
        $this->assertSame(1, $parameterWithMinimum->min());
        $parameterWithValue = $parameter->withAccept(3, 2, 1);
        $this->assertSame(null, $parameterWithValue->max());
        $this->assertSame(null, $parameterWithValue->min());
        $this->expectException(OverflowException::class);
        $parameterWithValue->withMin(0);
    }

    public function testWithMinimumOnArguments(): void
    {
        $expect = [
            'test' => 1,
        ];
        $parameter = (new IntParameter())->withMin(1);
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
        $parameter = new IntParameter();
        $withMaximum = $parameter->withMax(1);
        $this->assertNotSame($parameter, $withMaximum);
        $this->assertSame(1, $withMaximum->max());
        $withValue = $parameter->withAccept(1, 2, 3);
        $this->assertSame(null, $withValue->max());
        $this->assertSame(null, $withValue->min());
        $this->expectException(OverflowException::class);
        $withValue->withMax(0);
    }

    public function testWithMaximumOnArguments(): void
    {
        $expect = [
            'test' => 1,
        ];
        $parameter = (new IntParameter())->withMax(1);
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
        $parameter = new IntParameter();
        $with = $parameter
            ->withMin(1)
            ->withMax(2);
        $this->expectException(InvalidArgumentException::class);
        $with->withMin(2);
    }

    public function testWithMaximumMinimum(): void
    {
        $parameter = new IntParameter();
        $with = $parameter
            ->withMin(1)
            ->withMax(2);
        $this->expectException(InvalidArgumentException::class);
        $with->withMax(1);
    }

    public function testAssertCompatibleMinimum(): void
    {
        $value = 1;
        $parameter = (new IntParameter())->withMin($value);
        $compatible = (new IntParameter())->withMin($value);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $provided = $value * 2;
        $notCompatible = (new IntParameter())->withMin($provided);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Expected minimum value `{$value}`, provided `{$provided}`
            STRING
        );
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleMaximum(): void
    {
        $value = 1;
        $compatible = (new IntParameter())->withMax($value);
        $parameter = (new IntParameter())->withMax($value);
        $compatible = (new IntParameter())->withMax($value);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $provided = $value * 2;
        $notCompatible = (new IntParameter())->withMax($provided);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Expected maximum value `{$value}`, provided `{$provided}`
            STRING
        );
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleAcceptLeft(): void
    {
        $parameter = (new IntParameter())->withAccept(1, 2, 3);
        $compatible = (new IntParameter())->withAccept(3, 2, 1);
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $notCompatible = new IntParameter();
        $this->expectException(InvalidArgumentException::class);
        $expected = implode(', ', $parameter->accept());
        $this->getExpectedExceptionMessage(<<<PLAIN
        Expected value in [{$expected}]
        PLAIN);
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleAcceptRight(): void
    {
        $parameter = new IntParameter();
        $compatible = new IntParameter();
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $notCompatible = (new IntParameter())->withAccept(1, 2, 3);
        $this->expectException(InvalidArgumentException::class);
        $expected = implode(', ', $parameter->accept());
        $this->getExpectedExceptionMessage(<<<PLAIN
        Expected value in [{$expected}]
        PLAIN);
        $parameter->assertCompatible($notCompatible);
    }

    public function testAssertCompatibleAcceptMinimum(): void
    {
        $parameter = (new IntParameter())->withAccept(1, 2, 3);
        $notCompatible = (new IntParameter())->withMin(0);
        $this->expectException(InvalidArgumentException::class);
        $this->getExpectedExceptionMessage('value null');
        $parameter->assertCompatible($notCompatible);
    }

    public function testWithDefaultConflictMinimum(): void
    {
        $parameter = (new IntParameter())
            ->withMin(1)
            ->withDefault(1);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Default value `0` cannot be less than minimum value `1`
            STRING
        );
        $parameter->withDefault(0);
    }

    public function testWithDefaultConflictMaximum(): void
    {
        $parameter = (new IntParameter())
            ->withMax(1)
            ->withDefault(1);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Default value `2` cannot be greater than maximum value `1`
            STRING
        );
        $parameter->withDefault(2);
    }

    public function testWithDefaultConflictAccept(): void
    {
        $parameter = (new IntParameter())->withAccept(1, 2, 3);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Default value `5` must be in accept list `[1, 2, 3]`
            STRING
        );
        $parameter->withDefault(5);
    }

    public function testInvoke(): void
    {
        $value = 10;
        $parameter = new IntParameter();
        $this->assertSame($value, $parameter($value));
    }
}
