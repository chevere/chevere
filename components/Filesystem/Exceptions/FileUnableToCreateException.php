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

namespace Chevere\Components\Filesystem\Exceptions;

use Chevere\Components\ExceptionHandler\Exceptions\Exception;

/**
 * Exception thrown when unable to create a file.
 */
final class FileUnableToCreateException extends Exception
{
}
