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

use Chevere\Components\Parameter\Parameter;
use Chevere\Components\Type\Type;
use PHPUnit\Framework\TestCase;

final class ParameterTest extends TestCase
{
    public function testConstruct(): void
    {
        $type = new Type(Type::INTEGER);
        $name = 'parameter';
        $parameter = new Parameter($name, $type);
        $this->assertSame($name, $parameter->name());
        $this->assertEquals($type, $parameter->type());
    }
}
