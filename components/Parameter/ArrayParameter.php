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

namespace Chevere\Components\Parameter;

use Chevere\Components\Parameter\Traits\ParameterTrait;
use Chevere\Components\Type\Type;
use Chevere\Interfaces\Parameter\ArrayParameterInterface;
use Ds\Set;

final class ArrayParameter implements ArrayParameterInterface
{
    use ParameterTrait;

    private array $default = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertName();
        $this->type = new Type(Type::ARRAY);
        $this->attributes = new Set();
    }

    public function withDefault(array $default): ArrayParameterInterface
    {
        $new = clone $this;
        $new->default = $default;

        return $new;
    }

    public function default(): array
    {
        return $this->default;
    }
}
