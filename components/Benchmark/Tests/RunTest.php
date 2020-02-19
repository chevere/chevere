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
use Chevere\Components\Benchmark\Exceptions\ArgumentTypeException;
use Chevere\Components\Benchmark\Run;
use Chevere\Components\Benchmark\Runable;
use Chevere\Components\Benchmark\Interfaces\RunableInterface;
use PHPUnit\Framework\TestCase;

final class RunTest extends TestCase
{
    public function getRunable(): RunableInterface
    {
        return
            new Runable(
                (new Benchmark(100))
                    ->withAddedCallable('is_int', 'int?')
            );
    }

    public function testConstruct(): void
    {
        $run = new Run($this->getRunable());
        $this->assertSame(1, $run->times());
        $this->assertSame((int) ini_get('max_execution_time'), $run->timeLimit());
    }

    public function testWithTimes(): void
    {
        $times = 101;
        $run = (new Run($this->getRunable()))
            ->withTimes($times);
        $this->assertSame($times, $run->times());
    }

    public function testWithTimeLimit(): void
    {
        $timeLimit = 101;
        $run = (new Run($this->getRunable()))
            ->withTimeLimit($timeLimit);
        $this->assertSame($timeLimit, $run->timeLimit());
    }

    public function testExecBadArgumentType(): void
    {
        $argument = 'string';
        $this->expectException(ArgumentTypeException::class);
        $benchmark = (new Benchmark($argument))
            ->withAddedCallable(function (int $int) {
                return $int;
            }, 'int?');
        (new Run(
            new Runable($benchmark)
        ))
            ->exec();
    }

    public function testExec(): void
    {
        $this->expectNotToPerformAssertions();
        $benchmark = (new Benchmark(500, 3000))
            ->withAddedCallable(function (int $a, int $b) {
                return $a + $b;
            }, 'Add')
            ->withAddedCallable(function (int $a, int $b) {
                return $a / $b;
            }, 'Divide')
            ->withAddedCallable(function (int $a, int $b) {
                return $a * $b;
            }, 'Multiply');
        (new Run(new Runable($benchmark)))
            ->withTimes(20)
            ->exec()
            ->toString();
        // xdd($string);
    }
}
