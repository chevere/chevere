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
use function Chevere\Parameter\floatp;
use PHPUnit\Framework\TestCase;

final class FunctionsFloatTest extends TestCase
{
    public function testFloatp(): void
    {
        $parameter = floatp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame(-PHP_FLOAT_MIN, $parameter->minimum());
        $this->assertSame(PHP_FLOAT_MAX, $parameter->maximum());
        $this->assertSame([], $parameter->accept());
    }

    public function testFloatpOptions(): void
    {
        $description = 'test';
        $default = 5.0;
        $parameter = floatp(
            description: $description,
            default: $default,
            minimum: -100,
            maximum: 100,
        );
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame(-100.0, $parameter->minimum());
        $this->assertSame(100.0, $parameter->maximum());
        $parameter = floatp(accept: [0, 1]);
        $this->assertSame([0.0, 1.0], $parameter->accept());
    }

    public function testAssertFloat(): void
    {
        $parameter = floatp();
        assertFloat($parameter, 0);
        assertFloat($parameter, 0.0);
        assertArgument($parameter, 0);
        assertArgument($parameter, 0.0);
        $this->expectNotToPerformAssertions();
    }
}
