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

namespace Chevere\Exceptions\Http;

use Chevere\Exceptions\Core\Exception;

/**
 * Exception thrown when a method is not allowed.
 */
final class HttpMethodNotAllowedException extends Exception
{
}
