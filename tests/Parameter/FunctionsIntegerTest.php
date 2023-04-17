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
use function Chevere\Parameter\assertInteger;
use function Chevere\Parameter\integer;
use PHPUnit\Framework\TestCase;

final class FunctionsIntegerTest extends TestCase
{
    public function testIntegerp(): void
    {
        $parameter = integer();
        $this->assertSame(null, $parameter->description());
        $this->assertSame(null, $parameter->default());
        $this->assertSame(PHP_INT_MIN, $parameter->minimum());
        $this->assertSame(PHP_INT_MAX, $parameter->maximum());
        $this->assertSame([], $parameter->accept());
    }

    public function testIntegerpOptions(): void
    {
        $description = 'test';
        $default = 5;
        $parameter = integer(
            description: $description,
            default: $default,
            minimum: -100,
            maximum: 100,
        );
        $this->assertSame($description, $parameter->description());
        $this->assertSame($default, $parameter->default());
        $this->assertSame(-100, $parameter->minimum());
        $this->assertSame(100, $parameter->maximum());
        $parameter = integer(accept: [0, 1]);
        $this->assertSame([0, 1], $parameter->accept());
    }

    public function testAssertInteger(): void
    {
        $parameter = integer();
        $this->assertSame(0, assertInteger($parameter, 0));
        $this->assertSame(0, assertArgument($parameter, 0));
    }
}
