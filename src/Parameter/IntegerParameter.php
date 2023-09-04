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

use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Traits\NumericParameterTrait;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeInteger;

final class IntegerParameter implements IntegerParameterInterface
{
    use ParameterTrait;
    use NumericParameterTrait;

    private ?int $default = null;

    private ?int $minimum = null;

    private ?int $maximum = null;

    /**
     * @var int[]
     */
    private array $accept = [];

    public function withDefault(int $value): IntegerParameterInterface
    {
        $new = clone $this;
        $new->setDefault($value);

        return $new;
    }

    public function withMinimum(int $value): IntegerParameterInterface
    {
        $new = clone $this;
        $new->setMinimum($value, self::MAXIMUM);

        return $new;
    }

    public function withMaximum(int $value): IntegerParameterInterface
    {
        $new = clone $this;
        $new->setMaximum($value, self::MINIMUM);

        return $new;
    }

    public function withAccept(int ...$value): IntegerParameterInterface
    {
        $new = clone $this;
        $new->setAccept(...$value);

        return $new;
    }

    public function default(): ?int
    {
        return $this->default;
    }

    public function minimum(): ?int
    {
        return $this->minimum;
    }

    public function maximum(): ?int
    {
        return $this->maximum;
    }

    public function accept(): array
    {
        return $this->accept;
    }

    public function schema(): array
    {
        return [
            'type' => $this->type()->primitive(),
            'description' => $this->description(),
            'default' => $this->default(),
            'minimum' => $this->minimum(),
            'maximum' => $this->maximum(),
            'accept' => $this->accept(),
        ];
    }

    public function assertCompatible(IntegerParameterInterface $parameter): void
    {
        $this->assertNumericCompatible($parameter);
    }

    private function getType(): TypeInterface
    {
        return typeInteger();
    }
}
