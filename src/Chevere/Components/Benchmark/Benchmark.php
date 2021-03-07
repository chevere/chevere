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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Benchmark\BenchmarkInterface;
use Ds\Set;
use ReflectionFunction;

final class Benchmark implements BenchmarkInterface
{
    private array $arguments = [];

    private int $argumentsCount = 0;

    private Set $index;

    private Set $callables;

    private ReflectionFunction $reflection;

    public function __construct(...$namedArguments)
    {
        $this->arguments = $namedArguments;
        $this->argumentsCount = count($namedArguments);
        $this->callables = new Set();
        $this->index = new Set();
    }

    public function __clone()
    {
        $this->index = clone $this->index;
        $this->callables = clone $this->callables;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function callables(): Set
    {
        return $this->callables;
    }

    public function index(): Set
    {
        return $this->index;
    }

    public function withAddedCallable(callable ...$namedCallable): self
    {
        $new = clone $this;
        foreach ($namedCallable as $name => $callable) {
            $name = (string) $name;
            $new->assertUniqueCallableName($name);
            $new->reflection = new ReflectionFunction($callable);
            $new->assertCallableArgumentsCount($name);
            $new->index->add($name);
            $new->callables->add($callable);
        }

        return $new;
    }

    private function assertUniqueCallableName(string $name): void
    {
        if ($this->index->contains($name)) {
            throw new OverflowException(
                (new Message('Duplicate callable declaration %name%'))
                    ->code('%name%', $name)
            );
        }
    }

    private function assertCallableArgumentsCount(string $name): void
    {
        $parametersCount = $this->reflection->getNumberOfParameters();
        if ($this->argumentsCount !== $parametersCount) {
            throw new ArgumentCountException(
                (new Message('Instance of %className% was constructed to handle callables with %argumentsCount% arguments, %parametersCount% parameters declared for callable named %callableName%'))
                    ->code('%className%', __CLASS__)
                    ->code('%argumentsCount%', (string) $this->argumentsCount)
                    ->code('%parametersCount%', (string) $parametersCount)
                    ->code('%callableName%', $name)
            );
        }
    }
}
