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
 * Extends \ErrorException with Message support.
 */
class ErrorException extends \ErrorException
{
    use ExceptionTrait;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        MessageInterface $message,
        int $code = 0,
        int $severity = E_ERROR,
        string $filename = __FILE__,
        int $lineno = __LINE__,
        Throwable $previous = null
    ) {
        $this->_message = $message;
        parent::__construct($message->toString(), $code, $severity, $filename, $lineno, $previous);
    }
}
