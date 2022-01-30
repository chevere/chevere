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

namespace Chevere\Benchmark\Interfaces;

use Ds\Set;

/**
 * Describes the component in charge of providing a Benchmark for type callable.
 */
interface BenchmarkInterface
{
    public function __construct(...$namedArguments);

    public function arguments(): array;

    public function callables(): Set;

    public function index(): Set;

    public function withAddedCallable(callable ...$callables): self;
}
