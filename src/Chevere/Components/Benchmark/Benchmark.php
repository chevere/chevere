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
use ReflectionFunction;

final class Benchmark implements BenchmarkInterface
{
    private array $arguments = [];

    private int $argumentsCount = 0;

    private array $index;

    private array $callables;

    private $callable;

    private string $callableName;

    private ReflectionFunction $reflection;

    public function __construct()
    {
        $this->index = [];
        $this->callables = [];
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    public function withArguments(...$namedArguments): self
    {
        $new = clone $this;
        $new->arguments = $namedArguments;
        $new->argumentsCount = count($namedArguments);

        return $new;
    }

    public function withAddedCallable(callable ...$namedCallable): self
    {
        $new = clone $this;
        foreach ($namedCallable as $name => $callable) {
            $new->callable = $callable;
            $new->callableName = $name;
            $new->assertUniqueCallableName();
            $new->reflection = new ReflectionFunction($new->callable);
            $new->assertCallableArgumentsCount();
            $new->index[] = $new->callableName;
            $new->callables[] = $callable;
        }

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
        if (isset($this->index) && in_array($this->callableName, $this->index, true)) {
            throw new OverflowException(
                (new Message('Duplicate callable declaration %name%'))
                    ->code('%name%', $this->callableName)
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
            );
        }
    }
}
