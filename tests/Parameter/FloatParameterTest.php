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

use Chevere\Components\Parameter\FloatParameter;
use function Chevere\Components\Parameter\floatParameter;
use PHPUnit\Framework\TestCase;

final class FloatParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new FloatParameter('name');
        $this->assertEquals($parameter, floatParameter('name'));
        $this->assertSame(0.0, $parameter->default());
        $default = 12.34;
        $parameter = $parameter->withDefault($default);
        $this->assertSame($default, $parameter->default());
    }
}
