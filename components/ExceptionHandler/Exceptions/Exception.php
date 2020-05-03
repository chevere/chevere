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

namespace Chevere\Components\ExceptionHandler\Exceptions;

use Chevere\Components\ExceptionHandler\Traits\ExceptionTrait;
use Chevere\Components\Message\Interfaces\MessageInterface;
use Throwable;

/**
 * Extends \Exception with Message support.
 */
class Exception extends \Exception
{
    use ExceptionTrait;

    public function __construct(MessageInterface $message, int $code = 0, Throwable $previous = null)
    {
        $this->_message = $message;
        parent::__construct($message->toString(), $code, $previous);
    }
}
