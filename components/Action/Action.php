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

use Chevere\Components\Description\Traits\DescriptionTrait;
use Chevere\Components\Message\Message;
use Chevere\Components\Parameter\Parameters;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;

abstract class Action implements ActionInterface
{
    use DescriptionTrait;

    private string $description;

    private ParametersInterface $parameters;

    private ParametersInterface $responseDataParameters;

    public function getParameters(): ParametersInterface
    {
        return new Parameters;
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return new Parameters;
    }

    final public function __construct()
    {
        $this->description = $this->getDescription();
        $this->parameters = $this->getParameters();
        $this->responseDataParameters = $this->getResponseDataParameters();
    }

    final public function description(): string
    {
        return $this->description;
    }

    final public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    final public function responseDataParameters(): ParametersInterface
    {
        return $this->responseDataParameters;
    }

    final public function assertResponseDataParameters(array $namedArguments): void
    {
        /**
         * @var string $name
         * @var ParameterInterface $parameter
         */
        foreach ($this->responseDataParameters->getGenerator() as $name => $parameter) {
            if (!isset($namedArguments[$name])) {
                throw new OutOfBoundsException(
                    (new Message("Key %key% doesn't exists"))
                        ->code('%key%', $name)
                );
            }
            if (!$parameter->type()->validate($namedArguments[$name])) {
                throw new TypeException(
                    (new Message("Key %key% value doesn't validate the expected type %type%"))
                    ->code('%key%', $name)
                    ->code('%type%', $parameter->type()->typeHinting())
                );
            }
        }
    }
}
