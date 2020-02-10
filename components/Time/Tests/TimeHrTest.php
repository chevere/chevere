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

namespace Chevere\Components\Time\Tests;

use Chevere\Components\Time\TimeHr;
use PHPUnit\Framework\TestCase;

final class TimeHrTest extends TestCase
{
    public function testConstruct(): void
    {
        $timeHr = new TimeHr((int) hrtime(true));
        $this->assertStringEndsWith(' ms', $timeHr->toReadMs());
    }
}
