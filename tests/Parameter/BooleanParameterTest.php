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

use Chevere\Components\Parameter\BooleanParameter;
use PHPUnit\Framework\TestCase;

final class BooleanParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameter = new BooleanParameter('name');
        $this->assertSame(false, $parameter->default());
        $default = true;
        $parameter = $parameter->withDefault($default);
        $this->assertSame($default, $parameter->default());
    }
}
