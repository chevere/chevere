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

namespace Chevere\Benchmark\Tests;

use Chevere\Benchmark\Benchmark;
use Chevere\Benchmark\BenchmarkRun;
use Chevere\Benchmark\Interfaces\BenchmarkInterface;
use Chevere\Throwable\Errors\TypeError;
use PHPUnit\Framework\TestCase;

final class BenchmarkRunTest extends TestCase
{
    public function getBenchmark(): BenchmarkInterface
    {
        return (new Benchmark(100))
            ->withAddedCallable(int: 'is_int');
    }

    public function testConstruct(): void
    {
        $run = new BenchmarkRun($this->getBenchmark());
        $this->assertSame(1, $run->times());
        $this->assertSame((int) ini_get('max_execution_time'), $run->timeLimit());
    }

    public function testWithTimes(): void
    {
        $times = 100;
        $run = (new BenchmarkRun($this->getBenchmark()))
            ->withTimes($times);
        $this->assertSame($times, $run->times());
    }

    public function testWithTimeLimit(): void
    {
        $timeLimit = 100;
        $run = (new BenchmarkRun($this->getBenchmark()))
            ->withTimeLimit($timeLimit);
        $this->assertSame($timeLimit, $run->timeLimit());
    }

    public function testExecBadArgumentType(): void
    {
        $this->expectException(TypeError::class);
        $benchmark = (new Benchmark('string'))
            ->withAddedCallable(
                int: function (int $int) {
                    return $int;
                }
            );
        (new BenchmarkRun($benchmark))->exec();
    }

    public function testExec(): void
    {
        $this->expectNotToPerformAssertions();
        $benchmark = (new Benchmark(500, 3000))
            ->withAddedCallable(
                Add: function (int $a, int $b) {
                    return $a + $b;
                },
                Divide: function (int $a, int $b) {
                    return $a / $b;
                },
                Multiply: function (int $a, int $b) {
                    return $a * $b;
                }
            );
        (new BenchmarkRun($benchmark))
            ->withTimes(1000)
            ->exec()
            ->__toString();
    }
}
