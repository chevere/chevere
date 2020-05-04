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

namespace Chevere\Components\Str\Exceptions;

use Chevere\Components\ExceptionHandler\Exceptions\Exception;

/**
 * Exception thrown when the string contains a provided sub-string.
 */
final class StrContainsException extends Exception
{
}
