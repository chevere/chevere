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

namespace Chevere\Components\Action;

use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Action\ControllerInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;
use Chevere\Interfaces\Type\TypeInterface;

abstract class Controller extends Action implements ControllerInterface
{
    private ParametersInterface $contextParameters;

    private ArgumentsInterface $contextArguments;

    private TypeInterface $parametersType;

    abstract public function run(array $arguments): ResponseSuccessInterface;

    public function getContextParameters(): ParametersInterface
    {
        return new Parameters;
    }

    public function __construct()
    {
        parent::__construct();
        $this->contextParameters = $this->getContextParameters();
        $this->parametersType = new Type(self::PARAMETER_TYPE);
        $this->assertParametersType();
    }

    final public function assertParametersType(): void
    {
        $invalid = [];
        foreach ($this->parameters()->getGenerator() as $name => $parameter) {
            if ($parameter->type() != $this->parametersType) {
                $invalid[] = $name;
            }
        }
        if ($invalid !== []) {
            throw new InvalidArgumentException(
                (new Message('Parameter %parameters% must be of type %type% for controller %className%.'))
                    ->code('%parameters%', implode(', ', $invalid))
                    ->strong('%type%', $this->parametersType->typeHinting())
                    ->strong('%className%', static::class)
            );
        }
    }

    final public function withContextArguments(array $namedArguments): self
    {
        $new = clone $this;
        $new->contextArguments = new Arguments($new->contextParameters, $namedArguments);

        return $new;
    }

    final public function contextArguments(): ArgumentsInterface
    {
        return $this->contextArguments;
    }

    final public function hasContextArguments(): bool
    {
        return isset($this->contextArguments);
    }

    final public function contextParameters(): ParametersInterface
    {
        return $this->contextParameters;
    }
}
