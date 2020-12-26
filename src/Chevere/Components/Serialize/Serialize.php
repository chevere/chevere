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

namespace Chevere\Components\Serialize;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Serialize\SerializeInterface;
use Throwable;

final class Serialize implements SerializeInterface
{
    private string $serialize;

    public function __construct(mixed $var)
    {
        if (is_resource($var)) {
            throw new InvalidArgumentException(
                (new Message('Argument of type %type% is not supported.'))
                    ->code('%type%', 'resource')
            );
        }

        try {
            $this->serialize = serialize($var);
        } catch (Throwable $e) {
            throw new LogicException(
                new Message($e->getMessage())
            );
        }
    }

    public function toString(): string
    {
        return $this->serialize;
    }
}
