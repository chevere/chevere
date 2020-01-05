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
use PHPUnit\Framework\TestCase;

final class BenchmarkTest extends TestCase
{
    // $benchmark = (new Benchmark(10000))
    //     ->withArguments(500, 3000)
    //     ->withAddedCallable(function (int $a, int $b) {
    //         return $a + $b;
    //     }, 'Add')
    //     ->withAddedCallable(function (int $a, int $b) {
    //         return $a / $b;
    //     }, 'Divide')
    //     ->withAddedCallable(function (int $a, int $b) {
    //         return $a * $b;
    //     }, 'Multiply')
    //     ->exec();

    public function testConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        new Benchmark(1);
    }

    public function testWithTimeLimit(): void
    {
        $timeLimit = 101;
        $benchmark = (new Benchmark(1))->withTimeLimit($timeLimit);

        $this->assertSame($timeLimit, $benchmark->timeLimit());
    }

    public function testWithArguments(): void
    {
        $arguments = [1, false, '', null, 1.1];
        $benchmark = (new Benchmark(1))->withArguments(...$arguments);
        $this->assertSame($arguments, $benchmark->arguments());
    }

    public function testWithAddedCallable(): void
    {
        $callables = [
            'is_int' => 'is int',
            'is_string' => 'is string',
        ];
        $benchmark = new Benchmark(1);
        foreach ($callables as $callable => $name) {
            $benchmark = $benchmark->withAddedCallable($callable, $name);
            $this->assertContains($callable, $benchmark->callables());
            $this->assertContains($name, $benchmark->index());
        }
        $this->assertSame(array_keys($callables), $benchmark->callables());
        $this->assertSame(array_values($callables), $benchmark->index());
    }

    public function testExecNoCallables(): void
    {
        $this->expectException(NoCallablesException::class);
        (new Benchmark(10000))->exec();
    }

    public function testExecBadArgumentCount(): void
    {
        $arguments = [1, 2];
        $this->expectException(ArgumentCountException::class);
        (new Benchmark(1))
            ->withAddedCallable('is_bool', 'is bool')
            ->withArguments(...$arguments)
            ->exec();
    }

    public function testExecBadArgumentType(): void
    {
        $argument = 'string';
        $this->expectException(ArgumentTypeException::class);
        (new Benchmark(1))
            ->withAddedCallable(
                function (int $int) {
                    return $int;
                },
                'is int'
            )
            ->withArguments($argument)
            ->exec();
    }

    // public function testExecBadParameters(): void
    // {
    // }
}
