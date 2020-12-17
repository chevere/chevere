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
use Chevere\Interfaces\Message\MessageInterface;
use Throwable;

/**
 * Exception thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors.
 */
class UnexpectedValueException extends \UnexpectedValueException
{
    use ExceptionTrait;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(MessageInterface $message, int $code = 0, Throwable $previous = null)
    {
        $this->_message = $message;

        parent::__construct($this->_message->toString(), $code, $previous);
    }
}
