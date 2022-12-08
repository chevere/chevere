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
        $parameter1 = stringParameter();
        $parameter2 = integerParameter();
        $parameter = new ArrayParameter();
        $parameterWith = $parameter->withParameter(
            one: $parameter1,
            two: $parameter2
        );
        $this->assertNotSame($parameter, $parameterWith);
        $this->assertCount(2, $parameterWith->parameters());
    }
}
