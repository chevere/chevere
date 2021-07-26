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
use Chevere\Interfaces\Parameter\IntegerParameterInterface;
use Ds\Map;

/**
 * @method IntegerParameterInterface withDescription(string $description)
 * @method IntegerParameterInterface withAddedAttribute(string ...$attributes)
 * @method IntegerParameterInterface withoutAttribute(string ...$attribute)
 */
final class IntegerParameter implements IntegerParameterInterface
{
    use ParameterTrait;

    private int $default = 0;

    public function __construct(
        private string $description = ''
    ) {
        $this->type = new Type(Type::INTEGER);
        $this->attributes = new Map();
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
