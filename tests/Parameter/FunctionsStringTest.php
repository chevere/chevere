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
use function Chevere\Parameter\assertString;
use function Chevere\Parameter\stringp;
use PHPUnit\Framework\TestCase;

final class FunctionsStringTest extends TestCase
{
    public function testStringp(): void
    {
        $parameter = stringp();
        $this->assertSame('', $parameter->description());
        $this->assertSame(null, $parameter->default());
    }

    public function testAssertString(): void
    {
        $parameter = stringp();
        $this->assertSame('test', assertString($parameter, 'test'));
        $this->assertSame('0', assertArgument($parameter, '0'));
    }
}
