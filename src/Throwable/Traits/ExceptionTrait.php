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

namespace Chevere\Throwable\Traits;

use Chevere\Message\Interfaces\MessageInterface;
use Throwable;
use function Chevere\Message\message;

/**
 * @codeCoverageIgnore
 */
trait ExceptionTrait
{
    private MessageInterface $chevereMessage;

    public function __construct(null|string|MessageInterface $message = null, int $code = 0, Throwable $previous = null)
    {
        $this->chevereMessage = match (true) {
            $message === null => message(''),
            is_string($message) => message($message),
            $message instanceof MessageInterface => $message,
        };

        parent::__construct($this->chevereMessage->__toString(), $code, $previous);
    }

    public function message(): MessageInterface
    {
        return $this->chevereMessage;
    }
}
