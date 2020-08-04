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
use ReflectionClass;
use ReflectionMethod;

final class Task implements TaskInterface
{
    private string $action;

    private array $arguments;

    private ReflectionClass $reflection;

    private int $countParameters;

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
        $constructor = $this->reflection->getConstructor();
        if ($constructor === null) {
            $this->countParameters = 0;
        } else {
            $this->countParameters = $constructor->getNumberOfParameters();
        }
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

    private function assertArguments(): void
    {
        $count = count($this->arguments);
        if ($this->countParameters !== $count) {
            throw new ArgumentCountException(
                (new Message('Class %action% constructor expects %countParameters% arguments, %provided% provided'))
                    ->code('%action%', $this->action)
                    ->code('%countParameters%', (string) $this->countParameters)
                    ->code('%provided%', (string) $count)
            );
        }
    }
}
