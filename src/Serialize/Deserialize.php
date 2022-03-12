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
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;
use Throwable;

final class Deserialize implements DeserializeInterface
{
    private mixed $var;

    private TypeInterface $type;

    public function __construct(string $unserializable)
    {
        try {
            $this->var = unserialize($unserializable);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(previous: $e);
        }

        $this->type = new Type(get_debug_type($this->var));
    }

    public function var(): mixed
    {
        return $this->var;
    }

    public function type(): TypeInterface
    {
        return $this->type;
    }
}
