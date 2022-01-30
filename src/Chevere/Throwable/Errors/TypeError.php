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
 * Thrown when:
 *
 * 1. The value being set for a class property does not match the property's corresponding declared type.
 * 2. The argument type being passed to a function does not match its corresponding declared parameter type.
 * 3. A value being returned from a function does not match the declared function return type.
 */
final class TypeError extends \TypeError implements ThrowableInterface
{
    use ExceptionTrait;
}
