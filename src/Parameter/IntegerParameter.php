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

namespace Chevere\Parameter;

use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;

final class IntegerParameter implements IntegerParameterInterface
{
    use ParameterTrait;

    private int $default = 0;

    public function getType(): TypeInterface
    {
        return new Type(Type::INTEGER);
    }

    public function withDefault(int $value): IntegerParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function default(): int
    {
        return $this->default;
    }
}
