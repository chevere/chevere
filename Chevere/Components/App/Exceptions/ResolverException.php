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

namespace Chevere\Components\App\Exceptions;

use Exception;
use InvalidArgumentException;
use Throwable;
use Chevere\Components\Message\Message;
use Chevere\Components\Http\Interfaces\HttpStatusInterface;

/**
 * Exception thrown when unable to resolve.
 */
final class ResolverException extends Exception
{
    private $statuses = HttpStatusInterface::STATUSES;

    /**
     * Throws a new ResolverException exception.
     * @param string $message The exception message.
     * @param int $httpStatusCode An HTTP status code.
     * @param Throwable $previous
     */
    public function __construct(string $message, int $httpStatusCode, Throwable $previous = null)
    {
        if (!isset($this->statuses[$httpStatusCode])) {
            throw new InvalidArgumentException(
                (new Message('Exception %exception% expects a exception code matching an HTTP status code, argument %code% is not a valid HTTP status code'))
                    ->code('%exception%', __CLASS__)
                    ->code('%code%', $httpStatusCode)
                    ->toString()
            );
        }
        parent::__construct($message, $httpStatusCode, $previous);
    }
}
