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

use Chevere\Components\Parameter\IntegerParameter;
use PHPUnit\Framework\TestCase;

final class IntegerParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new IntegerParameter('name');
        $this->assertSame(0, $parameter->default());
        $default = 1234;
        $parameter = $parameter->withDefault($default);
        $this->assertSame($default, $parameter->default());
    }
}
