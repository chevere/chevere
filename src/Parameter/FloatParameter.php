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
use Chevere\Parameter\Traits\NumericParameterTrait;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeFloat;

final class FloatParameter implements FloatParameterInterface
{
    use ParameterTrait;
    use NumericParameterTrait;

    private ?float $default;

    private float $minimum = -PHP_FLOAT_MIN;

    private float $maximum = PHP_FLOAT_MAX;

    /**
     * @var float[]
     */
    private array $accept = [];

    public function withDefault(float $value): FloatParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function withMinimum(float $value): FloatParameterInterface
    {
        $new = clone $this;
        $new->setMinimum($value);

        return $new;
    }

    public function withMaximum(float $value): FloatParameterInterface
    {
        $new = clone $this;
        $new->setMaximum($value);

        return $new;
    }

    public function withAccept(float ...$value): FloatParameterInterface
    {
        $new = clone $this;
        $new->setAccept(...$value);

        return $new;
    }

    public function default(): ?float
    {
        return $this->default ?? null;
    }

    public function minimum(): float
    {
        return $this->minimum;
    }

    public function maximum(): float
    {
        return $this->maximum;
    }

    public function accept(): array
    {
        return $this->accept;
    }

    public function assertCompatible(FloatParameterInterface $parameter): void
    {
        $this->assertNumericCompatible($parameter);
    }

    private function getType(): TypeInterface
    {
        return typeFloat();
    }
}
