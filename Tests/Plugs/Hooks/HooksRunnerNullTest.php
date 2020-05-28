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

namespace Chevere\Tests\Hooks;

use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Components\Plugs\Hooks\HooksQueue;
use Chevere\Components\Plugs\Hooks\HooksRunnerNull;
use PHPUnit\Framework\TestCase;
use stdClass;

final class HooksRunnerNullTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugsQueue = new PlugsQueue(new HookPlugType);
        $hooksQueue = new HooksQueue($plugsQueue);
        $runner = new HooksRunnerNull($hooksQueue);
        $argument = new stdClass;
        $same = $argument;
        $runner->run('anchor', $argument);
        $this->assertSame($same, $argument);
    }
}
