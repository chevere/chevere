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

namespace Chevere\Components\Runtime\Tests\Sets;

use InvalidArgumentException;
use Chevere\Components\Runtime\Sets\SetDebug;
use PHPUnit\Framework\TestCase;

final class SetDebugTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetDebug('invalid argument');
    }

    public function testConstruct(): void
    {
        foreach (['0', '1'] as $val) {
            $set = new SetDebug($val);
            $this->assertSame('debug', $set->name());
            $this->assertSame($val, $set->value());
        }
    }
}
