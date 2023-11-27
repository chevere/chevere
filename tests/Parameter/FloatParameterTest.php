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

use Chevere\Parameter\FloatParameter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function Chevere\Parameter\float;

final class FloatParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new FloatParameter();
        $this->assertEquals($parameter, float());
        $this->assertSame(null, $parameter->default());
        $this->assertSame(null, $parameter->min());
        $this->assertSame(null, $parameter->max());
        $default = 12.34;
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'float',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
        $this->assertSame([
            'type' => 'float',
            'description' => '',
            'default' => $default,
            'minimum' => null,
            'maximum' => null,
            'accept' => [],
        ], $parameterWithDefault->schema());
    }

    public function testWithAccept(): void
    {
        $accept = [1.1, 2.2, 3.3];
        $parameter = new FloatParameter();
        $withValue = $parameter->withAccept(...$accept);
        $this->assertNotSame($parameter, $withValue);
        $this->assertSame($accept, $withValue->accept());
    }

    public function testWithMinimum(): void
    {
        $parameter = new FloatParameter();
        $value = 1.0;
        $parameterWith = $parameter->withMin($value);
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertSame($value, $parameterWith->min());
    }

    public function testWithMaximum(): void
    {
        $parameter = new FloatParameter();
        $value = 1.0;
        $parameterWith = $parameter->withMax($value);
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertSame($value, $parameterWith->max());
    }

    public function testAssertCompatible(): void
    {
        $parameter = (new FloatParameter())->withDefault(12.34);
        $compatible = new FloatParameter();
        $this->expectNotToPerformAssertions();
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
    }

    public function testAssertNotCompatible(): void
    {
        $value = 12.34;
        $provided = 56.78;
        $parameter = (new FloatParameter())->withAccept($value);
        $notCompatible = (new FloatParameter())->withAccept($provided);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            <<<STRING
            Expected value in `[{$value}]`, provided `{$provided}`
            STRING
        );
        $parameter->assertCompatible($notCompatible);
    }

    public function testInvoke(): void
    {
        $value = 10.0;
        $parameter = new FloatParameter();
        $this->assertSame($value, $parameter($value));
    }
}
