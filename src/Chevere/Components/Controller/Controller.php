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
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Type\TypeInterface;

abstract class Controller extends Action implements ControllerInterface
{
    protected TypeInterface $parametersType;

    public function __construct()
    {
        parent::__construct();

        $this->parametersType = new Type(self::PARAMETER_TYPE);
        $this->assertParametersType();
    }

    private function assertParametersType(): void
    {
        $invalid = [];
        foreach ($this->parameters()->getGenerator() as $name => $parameter) {
            if ($parameter->type()->validator() !== $this->parametersType->validator()) {
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
}
