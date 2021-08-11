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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Traits\ExceptionTrait;
use Chevere\Interfaces\Message\MessageInterface;
use Throwable;

/**
 * Exception thrown if a callback refers to an undefined function or if some arguments are missing.
 */
class BadFunctionCallException extends \BadFunctionCallException
{
    use ExceptionTrait;
}
