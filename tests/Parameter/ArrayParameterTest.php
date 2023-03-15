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
use function Chevere\Parameter\integerParameter;
use function Chevere\Parameter\stringParameter;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class ArrayParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new ArrayParameter();
        $this->assertSame([], $parameter->default());
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

    public function testWithParameter(): void
    {
        $string = stringParameter();
        $integer = integerParameter();
        $parameter = new ArrayParameter();
        $parameterWith = $parameter->withAddedRequired(
            one: $string,
            two: $integer
        );
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(2, $parameterWith->parameters());
    }

    public function testAssertCompatibleConflict(): void
    {
        $string = stringParameter();
        $integer = integerParameter();
        $parameter = (new ArrayParameter())->withAddedRequired(
            one: $string,
        );
        $altString = stringParameter();
        $compatible = (new ArrayParameter())->withAddedRequired(
            one: $altString,
        );
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
        $notCompatible = (new ArrayParameter())->withAddedRequired(
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
        $string = stringParameter();
        $parameter = (new ArrayParameter())->withAddedRequired(
            one: $string,
        );
        $notCompatible = (new ArrayParameter())->withAddedRequired(
            two: $string,
        );
        $this->expectException(OutOfBoundsException::class);
        $parameter->assertCompatible($notCompatible);
    }
}
