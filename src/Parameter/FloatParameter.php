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
use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\Parameter\Traits\NumericParameterTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class FloatParameter implements FloatParameterInterface
{
    use ParameterTrait;
    use NumericParameterTrait;

    private ?float $default = null;

    private ?float $min = null;

    private ?float $max = null;

    /**
     * @var float[]
     */
    private array $accept = [];

    public function __invoke(float $value): float
    {
        return assertFloat($this, $value);
    }

    public function withDefault(float $value): FloatParameterInterface
    {
        $new = clone $this;
        $new->setDefault($value);

        return $new;
    }

    public function withMin(float $value): FloatParameterInterface
    {
        $new = clone $this;
        $new->setMin($value, self::MAX);

        return $new;
    }

    public function withMax(float $value): FloatParameterInterface
    {
        $new = clone $this;
        $new->setMax($value, self::MIN);

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
        return $this->default;
    }

    public function min(): ?float
    {
        return $this->min;
    }

    public function max(): ?float
    {
        return $this->max;
    }

    public function accept(): array
    {
        return $this->accept;
    }

    public function schema(): array
    {
        return [
            'type' => $this->type->primitive(),
            'description' => $this->description(),
            'default' => $this->default(),
            'minimum' => $this->min(),
            'maximum' => $this->max(),
            'accept' => $this->accept(),
        ];
    }

    public function assertCompatible(FloatParameterInterface $parameter): void
    {
        $this->assertNumericCompatible($parameter);
    }

    private function getType(): TypeInterface
    {
        return new Type(Type::FLOAT);
    }
}
