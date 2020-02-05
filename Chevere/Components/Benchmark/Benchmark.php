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

namespace Chevere\Components\Benchmark;

use Chevere\Components\Benchmark\Exceptions\ArgumentCountException;
use Chevere\Components\Benchmark\Exceptions\DuplicatedCallableException;
use Chevere\Components\Message\Message;
use Chevere\Components\Benchmark\Interfaces\BenchmarkInterface;
use ReflectionFunction;

/**
 * Benchmark provides a prepared object for RunInterface.
 */
final class Benchmark implements BenchmarkInterface
{
    /** @var array Arguments that will be passed to callables */
    private array $arguments;

    private int $argumentsCount;

    /** @var array [id => $callableName] */
    private array $index;

    /** @var array [id => $callable] */
    private array $callables;

    /** @var callable */
    private $callable;

    private string $callableName;

    private ReflectionFunction $reflection;

    /**
     * Creates a new instance.
     *
     * @param array $arguments Arguments to pass to all callables.
     */
    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
        $this->argumentsCount = count($arguments);
        $this->index = [];
        $this->callables = [];
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function withAddedCallable(callable $callable, string $callableName): BenchmarkInterface
    {
        $this->callable = $callable;
        $this->callableName = $callableName;
        $this->assertUniqueCallableName();
        $this->reflection = new ReflectionFunction($this->callable);
        $this->assertCallableArgumentsCount();
        $new = clone $this;
        $new->index[] = $new->callableName;
        $new->callables[] = $callable;

        return $new;
    }

    public function callables(): array
    {
        return $this->callables;
    }

    public function index(): array
    {
        return $this->index;
    }

    private function assertUniqueCallableName(): void
    {
        if (isset($this->index) && in_array($this->callableName, $this->index)) {
            throw new DuplicatedCallableException(
                (new Message('Duplicate callable declaration %name%'))
                    ->code('%name%', $this->callableName)
                    ->toString()
            );
        }
    }

    private function assertCallableArgumentsCount(): void
    {
        $parametersCount = $this->reflection->getNumberOfParameters();
        if ($this->argumentsCount !== $parametersCount) {
            throw new ArgumentCountException(
                (new Message('Instance of %className% was constructed to handle callables with %argumentsCount% arguments, %parametersCount% parameters declared for callable named %callableName%'))
                    ->code('%className%', __CLASS__)
                    ->code('%argumentsCount%', (string) $this->argumentsCount)
                    ->code('%parametersCount%', (string) $parametersCount)
                    ->code('%callableName%', $this->callableName)
                    ->toString()
            );
        }
    }
}
