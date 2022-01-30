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

namespace Chevere\Throwable\Errors;

use Chevere\Throwable\Interfaces\ThrowableInterface;
use Chevere\Throwable\Traits\ExceptionTrait;

/**
 * Thrown when an error occurs while performing mathematical operations.
 *
 * These errors include attempting to perform a bitshift by a negative amount,
 * and any call to intdiv() that would result in a value outside the possible
 * bounds of an int.
 */
final class ArithmeticError extends \ArithmeticError implements ThrowableInterface
{
    use ExceptionTrait;
}
