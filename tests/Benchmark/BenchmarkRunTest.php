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

namespace Chevere\Components\Benchmark\Tests;

use Chevere\Components\Benchmark\Benchmark;
use Chevere\Components\Benchmark\BenchmarkRun;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Benchmark\BenchmarkInterface;
use PHPUnit\Framework\TestCase;

final class BenchmarkRunTest extends TestCase
{
    public function getBenchmark(): BenchmarkInterface
    {
        return (new Benchmark())
            ->withArguments(100)
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
        $this->expectException(TypeException::class);
        $benchmark = (new Benchmark())
            ->withArguments('string')
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
        $benchmark = (new Benchmark())
            ->withArguments(500, 3000)
            ->withAddedCallable(
                Add: function (int $a, int $b) {
                    return $a + $b;
                })
            ->withAddedCallable(
                Divide: function (int $a, int $b) {
                    return $a / $b;
                })
            ->withAddedCallable(
                Multiply: function (int $a, int $b) {
                    return $a * $b;
                });
        $string = (new BenchmarkRun($benchmark))
            ->withTimes(1000)
            ->exec()
            ->toString();
        // xdd($string);
    }
}
