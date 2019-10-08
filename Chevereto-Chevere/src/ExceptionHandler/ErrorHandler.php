<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\ExceptionHandler;

use ErrorException;

/**
 * The Chevere errors-as-exception handler.
 */
final class ErrorHandler
{
    public static function error($severity, $message, $file, $line): void
    {
        // throw new ErrorException($message, 0, $severity, $file, $line);
    }
}
