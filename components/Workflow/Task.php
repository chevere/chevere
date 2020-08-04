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
use ReflectionMethod;

final class Task implements TaskInterface
{
    private string $action;

    private array $arguments;

    private ReflectionFunction $reflection;

    public function __construct(string $action)
    {
        $this->action = $action;
        try {
            $this->reflection = new ReflectionFunction($this->action);
        } catch (\ReflectionException $e) {
            throw new InvalidArgumentException(
                (new Message("Function %action% doesn't exists"))
                    ->code('%action%', $this->action)
            );
        }
        $this->assertValidAction();
        $this->arguments = [];
    }

    public function withArguments(string ...$arguments): TaskInterface
    {
        $new = clone $this;
        $new->arguments = $arguments;
        $new->assertArguments();

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

    private function assertValidAction(): void
    {
        if ($this->reflection->getReturnType()->getName() !== ResponseInterface::class) {
            throw new UnexpectedValueException(
                (new Message('Action %action% must declare return value of type %interface%'))
                    ->code('%action%', $this->action)
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
                (new Message('Callable %action% expects %countParameters% arguments, %provided% provided'))
                    ->code('%action%', $this->action)
                    ->code('%countParameters%', (string) $countParameters)
                    ->code('%provided%', (string) $count)
            );
        }
    }
}
