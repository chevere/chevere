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

namespace Chevere\Components\Controller\Exceptions;

use Exception;

/**
 * Exception thrown when a controller doesn't implement the controller interface.
 */
final class ControllerInterfaceException extends Exception
{
}
