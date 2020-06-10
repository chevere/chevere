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

use Chevere\Components\Filesystem\Path;
use Chevere\Components\Plugin\PlugsQueue;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Components\Plugs\Hooks\HooksQueue;
use Chevere\Components\Plugs\Hooks\HooksRunner;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Tests\Plugs\Hooks\_resources\HooksRunnerTest\TestHookPath;
use Chevere\Tests\Plugs\Hooks\_resources\HooksRunnerTest\TestHookString;
use Chevere\Tests\Plugs\Hooks\_resources\HooksRunnerTest\TestHookTypeChange;
use PHPUnit\Framework\TestCase;
use stdClass;

final class HooksRunnerTest extends TestCase
{
    public function testConstruct(): void
    {
        $plugsQueue = new PlugsQueue(new HookPlugType);
        $hooksQueue = new HooksQueue($plugsQueue);
        $runner = new HooksRunner($hooksQueue);
        $argument = new stdClass;
        $same = $argument;
        $runner->run('anchor', $argument);
        $this->assertSame($same, $argument);
    }

    public function testRunHookedString(): void
    {
        $plugsQueue = new PlugsQueue(new HookPlugType);
        $plugsQueue = $plugsQueue->withAddedPlug(new TestHookString);
        $hooksQueue = new HooksQueue($plugsQueue);
        $runner = new HooksRunner($hooksQueue);
        $argument = 'string';
        $same = $argument;
        $runner->run('string', $argument);
        $this->assertSame("(hooked $same)", $argument);
    }

    public function testRunHookedObject(): void
    {
        $plugsQueue = new PlugsQueue(new HookPlugType);
        $plugsQueue = $plugsQueue->withAddedPlug(new TestHookPath);
        $hooksQueue = new HooksQueue($plugsQueue);
        $runner = new HooksRunner($hooksQueue);
        $argument = new Path(__DIR__);
        $runner->run('path', $argument);
        $this->assertEquals((new Path(__DIR__))->getChild('hooked/'), $argument);
    }

    public function testRunHookedTypeChange(): void
    {
        $plugsQueue = new PlugsQueue(new HookPlugType);
        $plugsQueue = $plugsQueue->withAddedPlug(new TestHookTypeChange);
        $hooksQueue = new HooksQueue($plugsQueue);
        $runner = new HooksRunner($hooksQueue);
        $argument = 'string';
        $this->expectException(RuntimeException::class);
        $runner->run('type', $argument);
    }
}
