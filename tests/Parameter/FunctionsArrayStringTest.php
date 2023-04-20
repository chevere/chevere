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

use function Chevere\Parameter\arrayp;
use function Chevere\Parameter\string;
use PHPUnit\Framework\TestCase;

final class FunctionsArrayStringTest extends TestCase
{
    public function testArraypString(): void
    {
        $string = string();
        $parameter = arrayp(a: $string);
        $this->assertCount(1, $parameter->items());
        $this->assertSame($string, $parameter->items()->get('a'));
        $this->assertTrue($parameter->items()->isRequired('a'));
    }
}
