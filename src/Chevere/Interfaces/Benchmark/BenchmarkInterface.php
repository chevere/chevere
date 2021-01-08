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

namespace Chevere\Interfaces\Benchmark;

/**
 * Describes the component in charge of defining a Benchmark for callable.
 */
interface BenchmarkInterface
{
    public function __construct();

    public function withArguments(...$namedArguments): self;

    public function withAddedCallable(callable ...$namedCallable): self;

    public function arguments(): array;

    public function callables(): array;

    public function index(): array;
}
