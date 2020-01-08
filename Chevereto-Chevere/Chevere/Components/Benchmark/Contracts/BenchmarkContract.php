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

namespace Chevere\Components\Benchmark\Contracts;

use Chevere\Components\Benchmark\Exceptions\DuplicatedCallableException;
use Chevere\Components\Benchmark\Exceptions\ArgumentCountException;

interface BenchmarkContract
{
    /**
     * @param array $arguments Arguments to pass to all callables.
     */
    public function __construct(...$arguments);

    public function arguments(): array;

    /**
     * Add a callable to the benchmark queue.
     *
     * @param callable $callable callable
     * @param string   $name     callable name, or alias for your own reference
     *
     * @throws DuplicatedCallableException If the $callableName has been already taken
     * @throws ArgumentCountException If the callable defines unmatched parameters for the construct arguments
     */
    public function withAddedCallable(callable $callable, string $callableName): BenchmarkContract;

    public function callables(): array;

    public function index(): array;
}
