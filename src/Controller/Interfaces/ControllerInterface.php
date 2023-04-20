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

/**
 * A Controller is an action that only accepts
 * string parameters on run method.
 */
interface ControllerInterface extends ActionInterface
{
}
