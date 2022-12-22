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

use Chevere\Message\Interfaces\MessageInterface;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OverflowException;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;

final class IntegerParameter implements IntegerParameterInterface
{
    use ParameterTrait;

    private int $default = 0;

    private ?int $minimum = PHP_INT_MIN;

    private ?int $maximum = PHP_INT_MAX;

    /**
     * @var int[]
     */
    private array $value = [];

    public function withDefault(int $value): IntegerParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function withMinimum(int $value): IntegerParameterInterface
    {
        $this->assertNoValueOverflow(
            message('Cannot set minimum value when value is set')
        );
        if (isset($this->maximum) && $value >= $this->maximum) {
            throw new InvalidArgumentException(
                message('Minimum value cannot be greater or equal than maximum value')
            );
        }
        $new = clone $this;
        $new->minimum = $value;

        return $new;
    }

    public function withMaximum(int $value): IntegerParameterInterface
    {
        $this->assertNoValueOverflow(
            message('Cannot set maximum value when value is set')
        );
        if (isset($this->minimum) && $value <= $this->minimum) {
            throw new InvalidArgumentException(
                message('Maximum value cannot be less or equal than minimum value')
            );
        }
        $new = clone $this;
        $new->maximum = $value;

        return $new;
    }

    public function withAccept(int ...$value): IntegerParameterInterface
    {
        $new = clone $this;
        $new->value = $value;
        $new->minimum = null;
        $new->maximum = null;

        return $new;
    }

    public function default(): int
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
        return $this->value;
    }

    public function assertCompatible(IntegerParameterInterface $parameter): void
    {
        if ($this->minimum() !== $parameter->minimum()) {
            throw new InvalidArgumentException(
                message('Expected minimum value %value%, provided %provided%')
                    ->withCode('%value%', strval($this->minimum() ?? 'null'))
                    ->withCode('%provided%', strval($parameter->minimum() ?? 'null'))
            );
        }
        if ($this->maximum() !== $parameter->maximum()) {
            throw new InvalidArgumentException(
                message('Expected maximum value %value%, provided %provided%')
                    ->withCode('%value%', strval($this->maximum() ?? 'null'))
                    ->withCode('%provided%', strval($parameter->maximum() ?? 'null'))
            );
        }
        if (array_diff($this->accept(), $parameter->accept()) !== []) {
            throw new InvalidArgumentException(
                message('Expecting accept %value%, provided %provided%')
                    ->withCode('%value%', implode(', ', $this->accept()))
                    ->withCode('%provided%', implode(', ', $parameter->accept()))
            );
        }
    }

    private function getType(): TypeInterface
    {
        return new Type(Type::INTEGER);
    }

    private function assertNoValueOverflow(MessageInterface $message): void
    {
        if ($this->value !== []) {
            throw new OverflowException($message);
        }
    }
}
