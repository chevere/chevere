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
use Chevere\Components\Benchmark\Exceptions\DuplicatedCallableException;
use PHPUnit\Framework\TestCase;

final class BenchmarkTest extends TestCase
{
    public function testConstruct(): void
    {
        $benchmark = new Benchmark();
        $this->assertSame([], $benchmark->arguments());
        $this->assertSame([], $benchmark->index());
        $this->assertSame([], $benchmark->callables());
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
        $benchmark = new Benchmark();
        foreach ($callables as $callable => $name) {
            $benchmark = $benchmark->withAddedCallable($callable, $name);
            $this->assertContains($callable, $benchmark->callables());
            $this->assertContains($name, $benchmark->index());
        }
        $this->assertSame(array_keys($callables), $benchmark->callables());
        $this->assertSame(array_values($callables), $benchmark->index());
    }

    public function testWithDuplicatedCallable(): void
    {
        $callableName = 'int?';
        $this->expectException(DuplicatedCallableException::class);
        (new Benchmark())
            ->withAddedCallable('is_int', $callableName)
            ->withAddedCallable('is_bool', $callableName);
    }
}
