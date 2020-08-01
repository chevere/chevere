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

use Chevere\Components\Parameter\Parameters;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

/**
 * @codeCoverageIgnore
 */
abstract class Controller implements ControllerInterface
{
    private ParametersInterface $parameters;

    private string $description;

    public function getParameters(): ParametersInterface
    {
        return new Parameters;
    }

    public function getDescription(): string
    {
        return '';
    }

    abstract public function run(ArgumentsInterface $arguments): ResponseInterface;

    final public function __construct()
    {
        $this->parameters = $this->getParameters();
        $this->description = $this->getDescription();
    }

    final public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    final public function description(): string
    {
        return $this->description;
    }
}
