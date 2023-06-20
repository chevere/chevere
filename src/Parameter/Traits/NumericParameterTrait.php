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
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OverflowException;
use function Chevere\Message\message;

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
        IntegerParameterInterface|FloatParameterInterface $parameter
    ): void {
        $this->assertNumericAccept($parameter);
        $this->assertNumericMinimum($parameter);
        $this->assertNumericMaximum($parameter);
    }

    private function assertNumericAccept(
        IntegerParameterInterface|FloatParameterInterface $parameter
    ): void {
        $diffA = array_diff($this->accept, $parameter->accept());
        $diffB = array_diff($parameter->accept(), $this->accept);
        if ($diffA !== [] || $diffB !== []) {
            $value = implode(', ', $this->accept());
            $provided = implode(', ', $parameter->accept());

            throw new InvalidArgumentException(
                message('Expected value in %value%, provided %provided%')
                    ->withCode('%value%', "[{$value}]")
                    ->withCode('%provided%', $provided)
            );
        }
    }

    private function assertNumericMinimum(
        IntegerParameterInterface|FloatParameterInterface $parameter
    ): void {
        if ($this->minimum !== $parameter->minimum()) {
            $value = strval($this->minimum() ?? 'null');
            $provided = strval($parameter->minimum() ?? 'null');

            throw new InvalidArgumentException(
                message('Expected minimum value %value%, provided %provided%')
                    ->withCode('%value%', $value)
                    ->withCode('%provided%', $provided)
            );
        }
    }

    private function assertNumericMaximum(
        IntegerParameterInterface|FloatParameterInterface $parameter
    ): void {
        if ($this->maximum !== $parameter->maximum()) {
            $value = strval($this->maximum() ?? 'null');
            $provided = strval($parameter->maximum() ?? 'null');

            throw new InvalidArgumentException(
                message('Expected maximum value %value%, provided %provided%')
                    ->withCode('%value%', $value)
                    ->withCode('%provided%', $provided)
            );
        }
    }
}
