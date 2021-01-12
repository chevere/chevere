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
use Ds\Set;

final class BooleanParameter implements BooleanParameterInterface
{
    use ParameterTrait;

    private bool $default = false;

    public function __construct()
    {
        $this->type = new Type(Type::BOOL);
        $this->attributes = new Set();
    }

    public function withDefault(bool $default): BooleanParameterInterface
    {
        $new = clone $this;
        $new->default = $default;

        return $new;
    }

    public function default(): bool
    {
        return $this->default;
    }
}
