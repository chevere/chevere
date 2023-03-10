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

use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeFloat;

final class FloatParameter implements FloatParameterInterface
{
    use ParameterTrait;

    private float $default = 0.0;

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

    /**
     * @codeCoverageIgnore
     */
    public function assertCompatible(FloatParameterInterface $parameter): void
    {
    }

    private function getType(): TypeInterface
    {
        return typeFloat();
    }
}
