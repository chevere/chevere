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
use Chevere\Components\Message\Message;
use Throwable;

/**
 * Exception that represents error in the program logic. This kind of exception should lead directly to a fix in your code.
 */
class LogicException extends \LogicException
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
