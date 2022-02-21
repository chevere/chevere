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

use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;

final class BooleanParameter implements BooleanParameterInterface
{
    use ParameterTrait;

    private bool $default = false;

    public function getType(): TypeInterface
    {
        return new Type(Type::BOOLEAN);
    }

    public function withDefault(bool $value): BooleanParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function default(): bool
    {
        return $this->default;
    }
}
