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

use Chevere\Components\Common\Traits\DescriptionTrait;
use Chevere\Components\Parameter\Arguments;
use Chevere\Components\Parameter\Parameters;
use Chevere\Components\Response\Response;
use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

abstract class Action implements ActionInterface
{
    use DescriptionTrait;

    protected ParametersInterface $parameters;

    protected ParametersInterface $responseDataParameters;

    public function __construct()
    {
        $this->setUp();
    }

    protected function setUp(): void
    {
        $this->description = $this->getDescription();
        $this->parameters = $this->getParameters();
        $this->responseDataParameters = $this->getResponseDataParameters();
    }

    public function getParameters(): ParametersInterface
    {
        return new Parameters();
    }

    public function getResponseDataParameters(): ParametersInterface
    {
        return new Parameters();
    }

    abstract public function run(ArgumentsInterface $arguments): ResponseInterface;

    final public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    final public function responseDataParameters(): ParametersInterface
    {
        return $this->responseDataParameters;
    }

    final public function getArguments(mixed ...$namedArguments): ArgumentsInterface
    {
        return new Arguments($this->parameters(), ...$namedArguments);
    }

    final public function getResponse(mixed ...$namedData): ResponseInterface
    {
        new Arguments($this->responseDataParameters, ...$namedData);

        return (new Response())->withData(...$namedData);
    }
}
