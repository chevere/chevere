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

namespace Chevere\Filesystem\Exceptions;

use Chevere\Throwable\Exception;

/**
 * Exception thrown when unable to get the file contents.
 */
final class FileUnableToGetException extends Exception
{
}
