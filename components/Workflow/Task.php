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

namespace Chevere\Components\Workflow;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\UnexpectedValueException;
use Chevere\Interfaces\Response\ResponseInterface;
use Chevere\Interfaces\Workflow\TaskInterface;
use ReflectionFunction;

final class Task implements TaskInterface
{
    private string $callable;

    private array $arguments;

    private ReflectionFunction $reflection;

    public function __construct(string $callable)
    {
        $this->callable = $callable;
        $this->assertIsCallable();
        $this->reflection = new ReflectionFunction($this->callable);
        $this->assertIsValidCallable();
        $this->arguments = [];
    }

    public function withArguments(string ...$arguments): TaskInterface
    {
        $new = clone $this;
        $new->arguments = $arguments;
        $new->assertArguments();

        return $new;
    }

    public function callable(): string
    {
        return $this->callable;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    private function assertIsCallable(): void
    {
        if (!is_callable($this->callable)) {
            throw new InvalidArgumentException(
                (new Message('Argument %callable% provided is not callable'))
                    ->code('%callable%', $this->callable)
            );
        }
    }

    private function assertIsValidCallable(): void
    {
        $type = $this->reflection->getReturnType();
        if ($type === null || $type->getName() !== ResponseInterface::class) {
            throw new UnexpectedValueException(
                (new Message('Callable %callable% must return an object implementing %interface%'))
                    ->code('%callable%', $this->callable)
                    ->code('%interface%', ResponseInterface::class)
            );
        }
    }

    private function assertArguments(): void
    {
        $count = count($this->arguments);
        $countParameters = $this->reflection->getNumberOfParameters();
        if ($countParameters !== $count) {
            throw new ArgumentCountException(
                (new Message('Callable %callable% expects %countParameters% arguments, %provided% provided'))
                    ->code('%callable%', $this->callable)
                    ->code('%countParameters%', (string) $countParameters)
                    ->code('%provided%', (string) $count)
            );
        }
    }
}
