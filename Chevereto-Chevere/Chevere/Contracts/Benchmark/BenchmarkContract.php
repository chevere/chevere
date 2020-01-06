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

namespace Chevere\Contracts\Benchmark;

interface BenchmarkContract
{
    /**
     * @param int $times Number of times to run each callable
     */
    public function __construct();

    /**
     * Set the callable arguments.
     */
    public function withArguments(...$arguments): BenchmarkContract;

    public function arguments(): array;

    /**
     * Add a callable to the benchmark queue.
     *
     * @param callable $callable callable
     * @param string   $name     callable name, or alias for your own reference
     */
    public function withAddedCallable(callable $callable, string $name): BenchmarkContract;

    public function callables(): array;

    public function index(): array;
}
