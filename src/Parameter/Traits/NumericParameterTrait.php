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

use Chevere\Message\Interfaces\MessageInterface;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OverflowException;

trait NumericParameterTrait
{
    private array $accept = [];

    private function errorAcceptOverflow(string $property): MessageInterface
    {
        return message('Cannot set %property% value when accept range is set')
            ->withTranslate('%property%', $property);
    }

    private function errorInvalidArgument(
        string $target,
        string $conflict
    ): MessageInterface {
        return message('Cannot set %target% value greater or equal than %conflict% value')
            ->withTranslate('%target%', $target)
            ->withTranslate('%conflict%', $conflict);
    }

    private function assertAcceptEmpty(MessageInterface $message): void
    {
        if ($this->accept !== []) {
            throw new OverflowException($message);
        }
    }

    private function setMinimum(int|float $value): void
    {
        $this->assertAcceptEmpty(
            $this->errorAcceptOverflow('minimum')
        );
        if ($value >= $this->maximum) {
            throw new InvalidArgumentException(
                $this->errorInvalidArgument('minimum', 'maximum')
            );
        }
        // @phpstan-ignore-next-line
        $this->minimum = $value;
    }

    private function setMaximum(int|float $value): void
    {
        $this->assertAcceptEmpty(
            $this->errorAcceptOverflow('maximum')
        );
        if ($value <= $this->minimum) {
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
        $lastKey = array_key_last($value);
        // @phpstan-ignore-next-line
        $this->accept = $value;
        // @phpstan-ignore-next-line
        $this->minimum = $value[0];
        // @phpstan-ignore-next-line
        $this->maximum = $value[$lastKey];
    }

    private function assertNumericCompatible(
        IntegerParameterInterface|FloatParameterInterface $parameter
    ): void {
        if ($this->minimum !== $parameter->minimum()) {
            $value = strval($this->minimum());
            $provided = strval($parameter->minimum());

            throw new InvalidArgumentException(
                message('Expected minimum value %value%, provided %provided%')
                    ->withCode('%value%', $value)
                    ->withCode('%provided%', $provided)
            );
        }
        if ($this->maximum !== $parameter->maximum()) {
            $value = strval($this->maximum());
            $provided = strval($parameter->maximum());

            throw new InvalidArgumentException(
                message('Expected maximum value %value%, provided %provided%')
                    ->withCode('%value%', $value)
                    ->withCode('%provided%', $provided)
            );
        }
        if (array_diff($this->accept, $parameter->accept()) !== []) {
            $value = implode(', ', $this->accept());
            $provided = implode(', ', $parameter->accept());

            throw new InvalidArgumentException(
                message('Expecting accept %value%, provided %provided%')
                    ->withCode('%value%', $value)
                    ->withCode('%provided%', $provided)
            );
        }
    }
}
