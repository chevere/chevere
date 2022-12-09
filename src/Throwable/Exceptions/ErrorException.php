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

namespace Chevere\Throwable\Exceptions;

use Chevere\Message\Interfaces\MessageInterface;
use Chevere\Throwable\Interfaces\ThrowableInterface;
use Chevere\Throwable\Traits\ExceptionTrait;
use Throwable;

/**
 * Extends \ErrorException with Message support.
 * @codeCoverageIgnore
 */
class ErrorException extends \ErrorException implements ThrowableInterface
{
    use ExceptionTrait;

    public function __construct(
        MessageInterface $message,
        int $code = 0,
        int $severity = E_ERROR,
        string $filename = __FILE__,
        int $lineno = __LINE__,
        Throwable $previous = null
    ) {
        $this->chevereMessage = $message;

        parent::__construct($message->__toString(), $code, $severity, $filename, $lineno, $previous);
    }
}
