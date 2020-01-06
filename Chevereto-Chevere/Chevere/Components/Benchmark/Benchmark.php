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

use Chevere\Components\Benchmark\Exceptions\ArgumentCountException;
use Chevere\Components\Benchmark\Exceptions\DuplicatedCallableException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Benchmark\BenchmarkContract;
use ReflectionFunction;

/**
 * Benchmark provides a prepared object for RunContract.
 */
final class Benchmark implements BenchmarkContract
{
    /** @var array Arguments that will be passed to callables */
    private array $arguments;

    private int $argumentsCount;

    /** @var array [id => $callableName] */
    private array $index;

    /** @var array [id => $callable] */
    private array $callables;

    /**
     * {@inheritdoc}
     */
    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
        $this->argumentsCount = count($arguments);
        $this->index = [];
        $this->callables = [];
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
        $reflection = new ReflectionFunction($callable);
        $parametersCount = $reflection->getNumberOfParameters();
        if ($this->argumentsCount !== $parametersCount) {
            throw new ArgumentCountException(
                (new Message('Instance of %className% was constructed to handle callables with %argumentsCount% arguments, %parametersCount% parameters declared for callable named %name%'))
                    ->code('%className%', __CLASS__)
                    ->code('%argumentsCount%', $this->argumentsCount)
                    ->code('%parametersCount%', $parametersCount)
                    ->code('%name%', $name)
                    ->toString()
            );
        }
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
