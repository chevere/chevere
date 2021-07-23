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
use Chevere\Interfaces\Parameter\BooleanParameterInterface;
use Ds\Map;

/**
 * @method BooleanParameterInterface withDescription(string $description)
 * @method BooleanParameterInterface withAddedAttribute(string ...$attributes)
 * @method BooleanParameterInterface withoutAttribute(string ...$attribute)
 */
final class BooleanParameter implements BooleanParameterInterface
{
    use ParameterTrait;

    private bool $default = false;

    public function __construct()
    {
        $this->type = new Type(Type::BOOLEAN);
        $this->attributes = new Map();
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
