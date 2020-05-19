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

namespace Chevere\Components\ExceptionHandler;

use Chevere\Components\Exception\ErrorException;
use Chevere\Components\Message\Message;

/**
 * The Chevere errors-as-exception handler.
 */
final class ErrorHandler
{
    public static function error(int $severity, string $message, string $file, int $line): void
    {
        throw new ErrorException(new Message($message), 0, $severity, $file, $line);
    }
}
