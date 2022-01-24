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
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Exceptions\OverflowException;
use PHPUnit\Framework\TestCase;

final class BenchmarkTest extends TestCase
{
    public function testConstruct(): void
    {
        $benchmark = new Benchmark();
        $this->assertSame([], $benchmark->arguments());
        $this->assertSame([], $benchmark->index()->toArray());
        $this->assertSame([], $benchmark->callables()->toArray());
    }

    public function testConstructWithArguments(): void
    {
        $arguments = [1, false, '', null, 1.1];
        $benchmark = (new Benchmark(...$arguments));
        $this->assertSame($arguments, $benchmark->arguments());
    }

    public function testClone(): void
    {
        $benchmark = new Benchmark('value');
        $clone = clone $benchmark;
        $this->assertNotSame($benchmark, $clone);
        $this->assertNotSame($benchmark->index(), $clone->index());
        $this->assertNotSame($benchmark->callables(), $clone->callables());
    }

    public function testWithBadAddedCallable(): void
    {
        $benchmark = (new Benchmark(1, 2, 3));
        $this->expectException(ArgumentCountError::class);
        $benchmark->withAddedCallable(
            one: function (int $one) {
                return $one;
            },
        );
    }

    public function testNamedArguments(): void
    {
        $arguments = [
            'one' => 1,
            'two' => '2',
        ];
        $benchmark = (new Benchmark(...$arguments))
            ->withAddedCallable(
                callable: function (int $one, string $two) {
                    return (string) $one . $two;
                },
            );
        $this->assertSame($arguments, $benchmark->arguments());
    }

    public function testWithAddedCallables(): void
    {
        $callables = [
            'is_int' => 'is int',
            'is_string' => 'is string',
        ];
        $benchmark = new Benchmark('value');
        foreach ($callables as $callable => $name) {
            $withAddedCallable = ($withAddedCallable ?? $benchmark)
                ->withAddedCallable(...[
                    $name => $callable,
                ]);
            $this->assertNotSame($benchmark, $withAddedCallable);
            $this->assertContains($callable, $withAddedCallable->callables());
            $this->assertContains($name, $withAddedCallable->index());
        }
        $this->assertSame(array_keys($callables), $withAddedCallable->callables()->toArray());
        $this->assertSame(array_values($callables), $withAddedCallable->index()->toArray());
    }

    public function testWithDuplicatedCallable(): void
    {
        $this->expectException(OverflowException::class);
        (new Benchmark('value'))
            ->withAddedCallable(int: 'is_int')
            ->withAddedCallable(int: 'is_int');
    }
}
