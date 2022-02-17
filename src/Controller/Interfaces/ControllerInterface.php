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

namespace Chevere\Controller\Interfaces;

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;

/**
 * Describes the component in charge of defining a controller, which is an action
 * intended to be exposed closest to an application entry-point HTTP/CLI mapping.
 *
 * Key point of a controller is that it only takes string arguments as input.
 */
interface ControllerInterface extends ActionInterface
{
    public function parameter(): StringParameterInterface;
}
