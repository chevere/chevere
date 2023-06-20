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

namespace Chevere\Serialize;

use Chevere\Serialize\Interfaces\SerializeInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Throwable;
use function Chevere\Message\message;

final class Serialize implements SerializeInterface
{
    private string $serialize;

    public function __construct(mixed $variable)
    {
        if (is_resource($variable)) {
            throw new InvalidArgumentException(
                message('Argument of type %type% is not supported.')
                    ->withCode('%type%', 'resource')
            );
        }

        try {
            $this->serialize = serialize($variable);
        } catch (Throwable $e) {
            throw new LogicException(previous: $e);
        }
    }

    public function __toString(): string
    {
        return $this->serialize;
    }
}
