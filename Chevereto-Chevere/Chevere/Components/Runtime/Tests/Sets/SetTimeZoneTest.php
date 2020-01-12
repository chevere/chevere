<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Runtime\Tests\Sets;

use InvalidArgumentException;
use Chevere\Components\Runtime\Sets\SetTimeZone;
use PHPUnit\Framework\TestCase;

final class SetTimeZoneTest extends TestCase
{
    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetTimeZone('invalid argument');
    }

    public function testConstruct(): void
    {
        foreach (['UTC', 'America/Santiago', 'Asia/Tokyo'] as $val) {
            $set = new SetTimeZone($val);
            $this->assertSame('timeZone', $set->name());
            $this->assertSame($val, $set->value());
        }
    }
}
