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
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\OverflowException;
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

    public function testConstructArguments(): void
    {
        $arguments = [1, false, '', null, 1.1];
        $benchmark = (new Benchmark(...$arguments));
        $this->assertSame($arguments, $benchmark->arguments());
    }

    public function testWithBadAddedCallable(): void
    {
        $benchmark = (new Benchmark(1, 2, 3));
        $this->expectException(ArgumentCountException::class);
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
            $benchmark = $benchmark->withAddedCallable(...[
                $name => $callable,
            ]);
            $this->assertContains($callable, $benchmark->callables());
            $this->assertContains($name, $benchmark->index());
        }
        $this->assertSame(array_keys($callables), $benchmark->callables()->toArray());
        $this->assertSame(array_values($callables), $benchmark->index()->toArray());
    }

    public function testWithDuplicatedCallable(): void
    {
        $this->expectException(OverflowException::class);
        (new Benchmark('value'))
            ->withAddedCallable(int: 'is_int')
            ->withAddedCallable(int: 'is_int');
    }
}
