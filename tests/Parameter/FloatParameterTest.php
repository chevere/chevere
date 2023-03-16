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
use PHPUnit\Framework\TestCase;

final class FloatParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new FloatParameter('name');
        $this->assertEquals($parameter, floatp('name'));
        $this->assertSame(0.0, $parameter->default());
        $default = 12.34;
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'float',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
    }

    public function testAssertCompatible(): void
    {
        $parameter = (new FloatParameter())->withDefault(12.34);
        $compatible = (new FloatParameter());
        $this->expectNotToPerformAssertions();
        $parameter->assertCompatible($compatible);
        $compatible->assertCompatible($parameter);
    }
}
