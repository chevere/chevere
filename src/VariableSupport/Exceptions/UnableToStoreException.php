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

namespace Chevere\VariableSupport\Exceptions;

use Chevere\Throwable\Exceptions\LogicException;

/**
 * Exception thrown when a variable can't be stored.
 */
final class UnableToStoreException extends LogicException
{
}
