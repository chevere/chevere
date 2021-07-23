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
use Chevere\Exceptions\Core\BadMethodCallException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\UnexpectedValueException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Workflow\StepInterface;
use ReflectionClass;
use ReflectionException;

final class Step implements StepInterface
{
    private array $arguments;

    private ParametersInterface $parameters;

    public function __construct(
        private string $action,
        mixed ...$namedArguments
    ) {
        try {
            $reflection = new ReflectionClass($this->action);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException(
                (new Message("Class %action% doesn't exists"))
                    ->code('%action%', $this->action)
            );
        }
        if (! $reflection->implementsInterface(ActionInterface::class)) {
            throw new UnexpectedValueException(
                (new Message('Action %action% must implement %interface% interface'))
                    ->code('%action%', $this->action)
                    ->code('%interface%', ActionInterface::class)
            );
        }
        $this->parameters = $reflection->newInstance()->getParameters();
        $this->arguments = [];
        if ($namedArguments !== []) {
            $this->setArguments(...$namedArguments);
        }
    }

    public function withArguments(mixed ...$namedArguments): StepInterface
    {
        $new = clone $this;
        $new->setArguments(...$namedArguments);

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

    private function setArguments(mixed ...$namedArguments): void
    {
        /** @var array<string, mixed> $namedArguments */
        $this->assertArgumentsCount($namedArguments);
        $store = [];
        $missing = [];
        foreach ($this->parameters->getGenerator() as $name => $parameter) {
            $parameter->description();
            $argument = $namedArguments[$name] ?? null;
            if ($argument !== null) {
                $store[$name] = $argument;
            } elseif ($this->parameters->isRequired($name)) {
                $missing[] = $name;
            }
        }
        if ($missing !== []) {
            throw new BadMethodCallException(
                (new Message('Missing argument(s) %arguments%'))
                    ->code('%arguments%', implode(', ', $missing))
            );
        }
        $this->arguments = $store;
    }

    private function assertArgumentsCount(array $arguments): void
    {
        $countPassed = count($arguments);
        $countRequired = count($this->parameters->required());
        if ($countRequired > $countPassed || $countRequired === 0 && $countPassed > 0) {
            throw new ArgumentCountException(
                (new Message('Method %action% expects %interface% providing %parametersCount% arguments, %given% given'))
                    ->code('%action%', $this->action . '::run')
                    ->code('%interface%', ArgumentsInterface::class)
                    ->code('%parametersCount%', (string) $countRequired)
                    ->code('%given%', (string) $countPassed)
            );
        }
    }
}
