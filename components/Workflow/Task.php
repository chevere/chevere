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
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Workflow\ActionInterface;
use Chevere\Interfaces\Workflow\TaskInterface;
use ReflectionClass;

final class Task implements TaskInterface
{
    private string $action;

    private array $arguments;

    private ReflectionClass $reflection;

    private ParametersInterface $parameters;

    public function __construct(string $action)
    {
        $this->action = $action;
        try {
            $this->reflection = new ReflectionClass($this->action);
        } catch (\ReflectionException $e) {
            throw new InvalidArgumentException(
                (new Message("Class %action% doesn't exists"))
                    ->code('%action%', $this->action)
            );
        }
        if (!$this->reflection->implementsInterface(ActionInterface::class)) {
            throw new UnexpectedValueException(
                (new Message('Action %action% must implement %interface% interface'))
                    ->code('%action%', $this->action)
                    ->code('%interface%', ActionInterface::class)
            );
        }
        $this->parameters = $this->reflection->newInstance()->parameters();
        $this->arguments = [];
    }

    public function withArguments(array $arguments): TaskInterface
    {
        $this->assertArgumentsCount($arguments);
        $store = [];
        $missing = [];
        foreach ($this->parameters->getGenerator() as $name => $parameter) {
            $argument = $arguments[$name] ?? null;
            if (!is_string($argument)) {
                $missing[] = $name;
                continue;
            }
            $store[$name] = $argument;
        }
        if ($missing !== []) {
            throw new ArgumentRequiredException(
                (new Message('Missing required argument(s): %message%'))
                    ->code('%message%', implode(', ', $missing))
            );
        }
        $new = clone $this;
        $new->arguments = $store;

        return $new;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }

    private function assertArgumentsCount(array $arguments): void
    {
        $count = count($arguments);
        if ($this->parameters->count() !== $count) {
            throw new ArgumentCountException(
                (new Message('Class %action% expects %parametersCount% arguments, %provided% provided'))
                    ->code('%action%', $this->action)
                    ->code('%parametersCount%', (string) $this->parameters->count())
                    ->code('%provided%', (string) $count)
            );
        }
    }
}
