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

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Regex\Regex;

abstract class Controller implements ControllerInterface
{
    private ControllerParametersInterface $parameters;

    final public function __construct()
    {
        $this->parameters = (new ControllerParameters)
            ->withPut(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            )
            ->withPut(
                new ControllerParameter('name', new Regex('/^[\w]+$/'))
            );
    }

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    public function parameters(): ControllerParametersInterface
    {
        return $this->parameters;
    }
}
