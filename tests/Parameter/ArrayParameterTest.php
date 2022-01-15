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

use Chevere\Components\Parameter\ArrayParameter;
use PHPUnit\Framework\TestCase;

final class ArrayParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new ArrayParameter('name');
        $this->assertSame([], $parameter->default());
        $default = ['test', 1];
        $parameterWithDefault = $parameter->withDefault($default);
        (new ParameterHelper())->testWithParameterDefault(
            primitive: 'array',
            parameter: $parameter,
            default: $default,
            parameterWithDefault: $parameterWithDefault
        );
    }
}
