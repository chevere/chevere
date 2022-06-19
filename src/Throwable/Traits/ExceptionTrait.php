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
use function Chevere\Message\message;
use Throwable;

/**
 * @codeCoverageIgnore
 */
trait ExceptionTrait
{
    private MessageInterface $_message;

    public function __construct(?MessageInterface $message = null, int $code = 0, Throwable $previous = null)
    {
        $this->_message = $message ?? message('');

        parent::__construct($this->_message->__toString(), $code, $previous);
    }

    public function message(): MessageInterface
    {
        return $this->_message;
    }
}
