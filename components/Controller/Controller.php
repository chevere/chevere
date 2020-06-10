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

use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\Controller\ControllerResponseInterface;

/**
 * @codeCoverageIgnore
 */
abstract class Controller implements ControllerInterface
{
    private ControllerParametersInterface $parameters;

    private string $description;

    public function getParameters(): ControllerParametersInterface
    {
        return new ControllerParameters;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    abstract public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface;

    final public function __construct()
    {
        $this->parameters = $this->getParameters();
        $this->description = $this->getDescription();
    }

    final public function parameters(): ControllerParametersInterface
    {
        return $this->parameters;
    }

    final public function description(): string
    {
        return $this->description;
    }
}
