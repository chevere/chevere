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
use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Response\ResponseSuccess;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseSuccessInterface;

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

    abstract public function run(array $arguments): ResponseSuccessInterface;

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

    final public function getArguments(array $arguments): ArgumentsInterface
    {
        return new Arguments($this->parameters, $arguments);
    }

    final public function getResponseSuccess(array $data): ResponseSuccessInterface
    {
        return new ResponseSuccess($this->responseDataParameters, $data);
    }
}
