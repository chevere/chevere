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

use Chevere\Components\Runtime\Sets\SetPrecision;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SetPrecisionTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetPrecision('invalid argument');
    }

    public function testConstruct(): void
    {
        foreach (['1', '16', '100'] as $val) {
            $set = new SetPrecision($val);
            $this->assertSame('precision', $set->name());
            $this->assertSame($val, $set->value());
        }
    }
}
