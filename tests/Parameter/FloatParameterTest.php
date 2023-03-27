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

use function Chevere\Parameter\floatp;
use Chevere\Parameter\FloatParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FloatParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new FloatParameter('name');
        $this->assertEquals($parameter, floatp('name'));
        $this->assertSame(0.0, $parameter->default());
        $this->assertSame(-PHP_FLOAT_MIN, $parameter->minimum());
        $this->assertSame(PHP_FLOAT_MAX, $parameter->maximum());
        $default = 12.34;
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'float',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
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
        $parameterWith = $parameter->withMinimum($value);
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertSame($value, $parameterWith->minimum());
    }

    public function testWithMaximum(): void
    {
        $parameter = new FloatParameter();
        $value = 1.0;
        $parameterWith = $parameter->withMaximum($value);
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertSame($value, $parameterWith->maximum());
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
            Expected value in [{$value}], provided {$provided}
            STRING
        );
        $parameter->assertCompatible($notCompatible);
    }
}
