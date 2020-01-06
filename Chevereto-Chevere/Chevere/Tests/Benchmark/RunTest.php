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

namespace Chevere\Tests\Benchmark;

use Chevere\Components\Benchmark\Benchmark;
use Chevere\Components\Benchmark\Exceptions\ArgumentCountException;
use Chevere\Components\Benchmark\Exceptions\ArgumentTypeException;
use Chevere\Components\Benchmark\Exceptions\NoCallablesException;
use Chevere\Components\Benchmark\Run;
use Chevere\Contracts\Benchmark\BenchmarkContract;
use PHPUnit\Framework\TestCase;

final class RunTest extends TestCase
{
    public function getBenchmark(): BenchmarkContract
    {
        return
            (new Benchmark())
                ->withAddedCallable('is_int', 'int?');
    }

    public function testBadConstruct(): void
    {
        $this->expectException(NoCallablesException::class);
        new Run(new Benchmark());
    }

    public function testConstruct(): void
    {
        $run = new Run($this->getBenchmark());
        $this->assertSame(1, $run->times());
        $this->assertSame((int) ini_get('max_execution_time'), $run->timeLimit());
    }

    public function testWithTimes(): void
    {
        $times = 101;
        $run = (new Run($this->getBenchmark()))
            ->withTimes($times);
        $this->assertSame($times, $run->times());
    }

    public function testWithTimeLimit(): void
    {
        $timeLimit = 101;
        $run = (new Run($this->getBenchmark()))
            ->withTimeLimit($timeLimit);
        $this->assertSame($timeLimit, $run->timeLimit());
    }

    public function testExecBadArgumentCount(): void
    {
        $arguments = [1, 2];
        $benchmark = $this->getBenchmark()
            ->withArguments(...$arguments);
        $this->expectException(ArgumentCountException::class);
        (new Run($benchmark))->exec();
    }

    public function testExecBadArgumentType(): void
    {
        $argument = 'string';
        $this->expectException(ArgumentTypeException::class);
        $benchmark = (new Benchmark())
            ->withAddedCallable(function (int $int) {
                return $int;
            }, 'int?')
            ->withArguments($argument);
        (new Run($benchmark))->exec();
    }

    public function testExec(): void
    {
        $this->expectNotToPerformAssertions();
        $benchmark = (new Benchmark())
            ->withArguments(500, 3000)
            ->withAddedCallable(function (int $a, int $b) {
                return $a + $b;
            }, 'Add')
            ->withAddedCallable(function (int $a, int $b) {
                return $a / $b;
            }, 'Divide')
            ->withAddedCallable(function (int $a, int $b) {
                return $a * $b;
            }, 'Multiply');
        $run = (new Run($benchmark))
            ->withTimes(1000);
        $run->exec()->toString();
    }
}
