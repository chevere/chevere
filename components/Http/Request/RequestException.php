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

namespace Chevere\Components\Http\Request;

use Exception;
use LogicException;
use Chevere\Components\Message\Message;
use Chevere\Components\Http\Interfaces\HttpStatusInterface;

final class RequestException extends Exception
{
    public function __construct(int $httpCode = 0, string $message = null, Exception $previous = null)
    {
        $status = HttpStatusInterface::STATUSES[$httpCode];

        if (!isset($status)) {
            throw new LogicException(
                (new Message('Unknown HTTP status code %code%'))
                    ->code('%code%', $httpCode)
                    ->toString()
            );
        }
        if (null === $message) {
            $message = $status;
        }
        parent::__construct($message, $httpCode, $previous);
    }
}
