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

use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;

final class ArrayParameter implements ArrayParameterInterface
{
    use ParameterTrait;

    /**
     * @var array<mixed, mixed>
     */
    private array $default = [];

    public function getType(): TypeInterface
    {
        return new Type(Type::ARRAY);
    }

    public function withDefault(array $value): ArrayParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function default(): array
    {
        return $this->default;
    }
}
