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

namespace Chevere\Interfaces\Controller;

use Chevere\Interfaces\Action\ActionInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;

/**
 * Describes the component in charge of defining a controller, which is an action
 * intended to be exposed closest to an application entry-point HTTP/CLI mapping.
 *
 * Key point of a controller is that it only takes string arguments and it
 * provides an additional layer for context parameters.
 */
interface ControllerInterface extends ActionInterface
{
    public function __construct(
        string $relation = '',
        string $dispatch = '',
    );

    public function relation(): string;

    public function parameter(): StringParameterInterface;
}
