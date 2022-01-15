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

namespace Chevere\Tests\HrTime;

use Chevere\Components\HrTime\HrTime;
use PHPUnit\Framework\TestCase;

final class HrTimeTest extends TestCase
{
    public function testToReadMs(): void
    {
        $decimal = '.';
        $thousands = ',';
        $group = 2;
        $now = (int) hrtime(true);
        $timeHr = new HrTime($now);
        $this->assertStringEndsWith(' ms', $timeHr->toReadMs());
        $chopMs = substr($timeHr->toReadMs(), 0, -($group + 1));
        $decimalHr = explode($decimal, $chopMs)[1];
        $this->assertSame(2, strlen($decimalHr));
        $this->assertSame(number_format($now / 1e+6, 2), $chopMs);
    }
}
