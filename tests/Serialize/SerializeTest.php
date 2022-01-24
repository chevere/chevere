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

namespace Chevere\Tests\Serialize;

use Chevere\Serialize\Serialize;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use PHPUnit\Framework\TestCase;

final class SerializeTest extends TestCase
{
    public function testResourceArgument(): void
    {
        $variable = fopen('php://temp', 'r+');
        $this->expectException(InvalidArgumentException::class);
        new Serialize($variable);
    }

    public function testObjectAnonArgument(): void
    {
        $variable = new class() {
        };
        $this->expectException(LogicException::class);
        new Serialize($variable);
    }

    public function testToString(): void
    {
        $variable = 'test';
        $serialize = new Serialize($variable);
        $this->assertSame(serialize($variable), $serialize->__toString());
    }
}
