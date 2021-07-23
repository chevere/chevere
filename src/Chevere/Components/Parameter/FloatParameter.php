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
use Chevere\Interfaces\Parameter\FloatParameterInterface;
use Ds\Map;

/**
 * @method FloatParameterInterface withDescription(string $description)
 * @method FloatParameterInterface withAddedAttribute(string ...$attributes)
 * @method FloatParameterInterface withoutAttribute(string ...$attribute)
 */
final class FloatParameter implements FloatParameterInterface
{
    use ParameterTrait;

    private float $default = 0.0;

    public function __construct()
    {
        $this->type = new Type(Type::FLOAT);
        $this->attributes = new Map();
    }

    public function withDefault(float $value): FloatParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function default(): float
    {
        return $this->default;
    }
}
