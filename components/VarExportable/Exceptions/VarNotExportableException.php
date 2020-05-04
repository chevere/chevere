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

namespace Chevere\Components\VarExportable\Exceptions;

use Chevere\Components\ExceptionHandler\Exceptions\Exception;

/**
 * Exception thrown when a variable is not exportable.
 */
final class VarNotExportableException extends Exception
{
}
