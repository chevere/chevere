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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Common\Traits\DescriptionTrait;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Parameters;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;

abstract class Action implements ActionInterface
{
    use DescriptionTrait;

    protected string $description = '';

    protected ParametersInterface $parameters;

    protected ParametersInterface $responseParameters;

    public function __construct()
    {
        $this->setUp();
    }

    protected function setUp(): void
    {
        $this->description = $this->getDescription();
        $this->parameters = $this->getParameters();
        $this->responseParameters = $this->getResponseParameters();
    }

    public function getParameters(): ParametersInterface
    {
        return new Parameters();
    }

    public function getResponseParameters(): ParametersInterface
    {
        return new Parameters();
    }

    abstract public function run(ArgumentsInterface $arguments): ResponseInterface;

    final public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    final public function responseParameters(): ParametersInterface
    {
        return $this->responseParameters;
    }

    final public function getArguments(mixed ...$namedArguments): ArgumentsInterface
    {
        return new Arguments($this->parameters(), ...$namedArguments);
    }

    final public function getResponse(mixed ...$namedData): ResponseInterface
    {
        new Arguments($this->responseParameters, ...$namedData);

        return new Response(...$namedData);
    }
}
