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

namespace Chevere\Tests\Pluggable\Plugs\Hooks;

use Chevere\Components\Filesystem\Path;
use Chevere\Components\Pluggable\Plugs\Hooks\HooksQueue;
use Chevere\Components\Pluggable\Plugs\Hooks\HooksRunner;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Tests\Pluggable\Plugs\Hooks\_resources\HooksRunnerTest\TestHookPath;
use Chevere\Tests\Pluggable\Plugs\Hooks\_resources\HooksRunnerTest\TestHookString;
use Chevere\Tests\Pluggable\Plugs\Hooks\_resources\HooksRunnerTest\TestHookTypeChange;
use PHPUnit\Framework\TestCase;
use stdClass;

final class HooksRunnerTest extends TestCase
{
    public function testConstruct(): void
    {
        $hooksQueue = new HooksQueue();
        $runner = new HooksRunner($hooksQueue);
        $argument = new stdClass();
        $same = $argument;
        $runner->run('anchor', $argument);
        $this->assertSame($same, $argument);
    }

    public function testRunHookedString(): void
    {
        $hooksQueue = (new HooksQueue())->withAdded(new TestHookString());
        $runner = new HooksRunner($hooksQueue);
        $argument = 'string';
        $same = $argument;
        $runner->run('string', $argument);
        $this->assertSame("(hooked ${same})", $argument);
    }

    public function testRunHookedObject(): void
    {
        $hooksQueue = (new HooksQueue())->withAdded(new TestHookPath());
        $runner = new HooksRunner($hooksQueue);
        $argument = new Path(__DIR__);
        $runner->run('path', $argument);
        $this->assertEqualsCanonicalizing(
            (new Path(__DIR__))->getChild('hooked/'),
            $argument
        );
    }

    public function testRunHookedTypeChange(): void
    {
        $hooksQueue = (new HooksQueue())->withAdded(new TestHookTypeChange());
        $runner = new HooksRunner($hooksQueue);
        $argument = 'string';
        $this->expectException(InvalidArgumentException::class);
        $runner->run('type', $argument);
    }
}
