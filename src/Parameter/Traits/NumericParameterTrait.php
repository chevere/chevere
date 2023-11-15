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

namespace Chevere\Parameter\Traits;

use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntParameterInterface;
use InvalidArgumentException;
use OverflowException;
use function Chevere\Message\message;

trait NumericParameterTrait
{
    private array $accept = [];

    private function errorAcceptOverflow(string $property): string
    {
        return strtr(
            'Cannot set %property% value when accept range is set',
            [
                '%property%' => $property,
            ]
        );
    }

    private function errorInvalidArgument(
        string $target,
        string $conflict
    ): string {
        return strtr(
            'Cannot set %target% value greater or equal than %conflict% value',
            [
                '%target%' => $target,
                '%conflict%' => $conflict,
            ]
        );
    }

    private function assertAcceptEmpty(string $message): void
    {
        if ($this->accept !== []) {
            throw new OverflowException($message);
        }
    }

    private function setDefault(int|float $value): void
    {
        if (isset($this->minimum) && $value < $this->minimum) {
            throw new InvalidArgumentException(
                (string) message(
                    'Default value `%value%` cannot be less than minimum value `%minimum%`',
                    value: strval($value),
                    minimum: strval($this->minimum),
                )
            );
        }
        if (isset($this->maximum) && $value > $this->maximum) {
            throw new InvalidArgumentException(
                (string) message(
                    'Default value `%value%` cannot be greater than maximum value `%maximum%`',
                    value: strval($value),
                    maximum: strval($this->maximum),
                )
            );
        }
        if ($this->accept !== [] && ! in_array($value, $this->accept, true)) {
            $list = implode(', ', $this->accept);

            throw new InvalidArgumentException(
                (string) message(
                    'Default value `%value%` must be in accept list `%accept%`',
                    value: strval($value),
                    accept: "[{$list}]",
                )
            );
        }
        // @phpstan-ignore-next-line
        $this->default = $value;
    }

    private function setMinimum(int|float $value, int|float $maximum): void
    {
        $this->assertAcceptEmpty(
            $this->errorAcceptOverflow('minimum')
        );
        if ($value >= ($this->maximum ?? $maximum)) {
            throw new InvalidArgumentException(
                $this->errorInvalidArgument('minimum', 'maximum')
            );
        }
        // @phpstan-ignore-next-line
        $this->minimum = $value;
    }

    private function setMaximum(int|float $value, int|float $minimum): void
    {
        $this->assertAcceptEmpty(
            $this->errorAcceptOverflow('maximum')
        );
        if ($value <= ($this->minimum ?? $minimum)) {
            throw new InvalidArgumentException(
                $this->errorInvalidArgument('maximum', 'minimum')
            );
        }
        // @phpstan-ignore-next-line
        $this->maximum = $value;
    }

    private function setAccept(int|float ...$value): void
    {
        sort($value);
        // @phpstan-ignore-next-line
        $this->accept = $value;
        $this->minimum = null;
        $this->maximum = null;
    }

    private function assertNumericCompatible(
        IntParameterInterface|FloatParameterInterface $parameter
    ): void {
        $this->assertNumericAccept($parameter);
        $this->assertNumericMinimum($parameter);
        $this->assertNumericMaximum($parameter);
    }

    private function assertNumericAccept(
        IntParameterInterface|FloatParameterInterface $parameter
    ): void {
        $diff = array_merge(
            array_diff($this->accept, $parameter->accept()),
            array_diff($parameter->accept(), $this->accept)
        );
        if ($diff !== []) {
            $value = implode(', ', $this->accept());
            $provided = implode(', ', $parameter->accept());

            throw new InvalidArgumentException(
                (string) message(
                    'Expected value in `%accept%`, provided `%provided%`',
                    accept: "[{$value}]",
                    provided: $provided,
                )
            );
        }
    }

    private function assertNumericMinimum(
        IntParameterInterface|FloatParameterInterface $parameter
    ): void {
        if ($this->minimum !== $parameter->minimum()) {
            $value = strval($this->minimum() ?? 'null');
            $provided = strval($parameter->minimum() ?? 'null');

            throw new InvalidArgumentException(
                (string) message(
                    'Expected minimum value `%value%`, provided `%provided%`',
                    value: $value,
                    provided: $provided,
                )
            );
        }
    }

    private function assertNumericMaximum(
        IntParameterInterface|FloatParameterInterface $parameter
    ): void {
        if ($this->maximum !== $parameter->maximum()) {
            $value = strval($this->maximum() ?? 'null');
            $provided = strval($parameter->maximum() ?? 'null');

            throw new InvalidArgumentException(
                (string) message(
                    'Expected maximum value `%value%`, provided `%provided%`',
                    value: $value,
                    provided: $provided
                )
            );
        }
    }
}
