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

namespace Chevere\Components\Benchmark;

use Chevere\Components\Benchmark\Exceptions\DuplicatedCallableException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Benchmark\BenchmarkContract;

/**
 * Benchmark provides a prepared object for RunContract.
 */
final class Benchmark implements BenchmarkContract
{
    /** @var array Arguments that will be passed to callables */
    private array $arguments;

    /** @var array [id => $callableName] */
    private array $index;

    /** @var array [id => $callable] */
    private array $callables;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->arguments = [];
        $this->index = [];
        $this->callables = [];
    }

    /**
     * {@inheritdoc}
     */
    public function withArguments(...$arguments): BenchmarkContract
    {
        $new = clone $this;
        $new->arguments = $arguments;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedCallable(callable $callable, string $name): BenchmarkContract
    {
        $new = clone $this;
        if (isset($new->index) && in_array($name, $new->index)) {
            throw new DuplicatedCallableException(
                (new Message('Duplicate callable declaration %name%'))
                    ->code('%name%', $name)
                    ->toString()
            );
        }
        $new->index[] = $name;
        $new->callables[] = $callable;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function callables(): array
    {
        return $this->callables;
    }

    /**
     * {@inheritdoc}
     */
    public function index(): array
    {
        return $this->index;
    }
}
