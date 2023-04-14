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

use function Chevere\Parameter\arraypoString;
use function Chevere\Parameter\arraypString;
use function Chevere\Parameter\string;
use PHPUnit\Framework\TestCase;

final class FunctionsArrayStringTest extends TestCase
{
    public function testArraypString(): void
    {
        $string = string();
        $parameter = arraypString(a: $string);
        $this->assertCount(1, $parameter->parameters());
        $this->assertSame($string, $parameter->parameters()->get('a'));
        $this->assertTrue($parameter->parameters()->isRequired('a'));
    }

    public function testArraypoString(): void
    {
        $string = string();
        $parameter = arraypoString(a: $string);
        $this->assertCount(1, $parameter->parameters());
        $this->assertSame($string, $parameter->parameters()->get('a'));
        $this->assertTrue($parameter->parameters()->isOptional('a'));
    }
}
