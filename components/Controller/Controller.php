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

use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Controller\Interfaces\ControllerResponseInterface;

/**
 * @codeCoverageIgnore
 */
abstract class Controller implements ControllerInterface
{
    protected ControllerParametersInterface $parameters;

    final public function __construct()
    {
        $this->parameters = $this->getParameters();
    }

    final public function parameters(): ControllerParametersInterface
    {
        return $this->parameters;
    }

    public function getParameters(): ControllerParametersInterface
    {
        return new ControllerParameters;
    }

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    abstract public function run(ControllerArgumentsInterface $arguments): ControllerResponseInterface;
}
