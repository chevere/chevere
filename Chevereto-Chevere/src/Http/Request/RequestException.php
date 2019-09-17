<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Http\Request;

use Exception;
use LogicException;
use Chevere\Http\Http;
use Chevere\Message\Message;

final class RequestException extends Exception
{
    public function __construct(int $code = 0, string $message = null, Exception $previous = null)
    {
        $status = Http::STATUS_CODES[$code];

        if (!isset($status)) {
            throw new LogicException(
                (new Message('Unknown HTTP status code %code%.'))
                    ->code('%code%', $code)
                    ->toString()
            );
        }
        if (null == $message) {
            $message = $status;
        }
        parent::__construct($message, $code, $previous);
    }
}
