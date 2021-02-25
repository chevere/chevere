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
        $timeHr = new HrTime((int) hrtime(true));
        $this->assertStringEndsWith(' ms', $timeHr->toReadMs());
    }
}
