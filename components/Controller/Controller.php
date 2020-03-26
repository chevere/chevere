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
    final public function __construct()
    {
    }

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    public function parameters(): ControllerParametersInterface
    {
        return (new ControllerParameters)
            ->put(
                new ControllerParameter('id', new Regex('/^[0-9]+$/'))
            )
            ->put(
                new ControllerParameter('name', new Regex('/^[\w]+$/'))
            );
    }
}
