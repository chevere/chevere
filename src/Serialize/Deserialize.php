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

use Chevere\Serialize\Interfaces\DeserializeInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Throwable;

final class Deserialize implements DeserializeInterface
{
    private mixed $variable;

    public function __construct(string $unserializable)
    {
        try {
            $this->variable = unserialize($unserializable);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(previous: $e);
        }
    }

    public function variable(): mixed
    {
        return $this->variable;
    }
}
