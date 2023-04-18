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

use function Chevere\Parameter\assertArgument;
use function Chevere\Parameter\assertFloat;
use function Chevere\Parameter\float;
use PHPUnit\Framework\TestCase;

final class FunctionsFloatTest extends TestCase
{
    public function testFloat(): void
    {
        $parameter = float();
        $this->assertSame(null, $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame(null, $parameter->minimum());
        $this->assertSame(null, $parameter->maximum());
        $this->assertSame([], $parameter->accept());
    }

    public function testFloatOptions(): void
    {
        $description = 'test';
        $default = 5.0;
        $parameter = float(
            description: $description,
            default: $default,
            minimum: -100,
            maximum: 100,
        );
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame(-100.0, $parameter->minimum());
        $this->assertSame(100.0, $parameter->maximum());
        $parameter = float(accept: [0, 1]);
        $this->assertSame([0.0, 1.0], $parameter->accept());
    }

    public function testAssertFloat(): void
    {
        $parameter = float();
        $this->assertSame(0.0, assertFloat($parameter, 0));
        $this->assertSame(0.0, assertFloat($parameter, 0.0));
        $this->assertSame(0.0, assertArgument($parameter, 0));
        $this->assertSame(0.0, assertArgument($parameter, 0.0));
    }
}
