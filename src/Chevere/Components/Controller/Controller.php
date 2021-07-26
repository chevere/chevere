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

namespace Chevere\Components\Controller;

use Chevere\Components\Action\Action;
use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\StringParameter;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;

abstract class Controller extends Action implements ControllerInterface
{
    protected StringParameterInterface $parameterType;

    final public function __construct(
        protected string $relation = ''
    ) {
        $this->parameterType ??= $this->parameter();
        $this->setUp();
        $this->assertParametersType();
    }

    public function parameter(): StringParameterInterface
    {
        return new StringParameter();
    }

    final public function relation(): string
    {
        return $this->relation;
    }

    private function assertParametersType(): void
    {
        $invalid = [];
        foreach ($this->parameters()->getGenerator() as $name => $parameter) {
            if ($parameter->type()->validator() !== $this->parameterType->type()->validator()) {
                $invalid[] = $name;
            }
        }
        if ($invalid !== []) {
            throw new InvalidArgumentException(
                (new Message('Parameter %parameters% must be of type %type% for controller %className%.'))
                    ->code('%parameters%', implode(', ', $invalid))
                    ->strong('%type%', $this->parameterType->type()->typeHinting())
                    ->strong('%className%', static::class)
            );
        }
    }
}
