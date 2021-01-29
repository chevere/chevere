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

namespace Chevere\Exceptions\Core;

use Chevere\Exceptions\Core\Traits\ExceptionTrait;

/**
 * Exception thrown when an illegal index was requested. This represents errors that should be detected at compile time.
 */
class OutOfRangeException extends \OutOfRangeException
{
    use ExceptionTrait;
}
