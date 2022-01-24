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

namespace Chevere\Router\Exceptions;

use Chevere\Throwable\Exception;

/**
 * Exception thrown when a route can't be found.
 */
final class RouteNotFoundException extends Exception
{
}
