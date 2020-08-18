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

namespace Chevere\Components\Permission;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfRangeException;
use Chevere\Interfaces\Permission\RangeInterface;

abstract class Range implements RangeInterface
{
    private ?int $min;

    private ?int $max;

    public function getMin(): ?int
    {
        return null;
    }

    public function getMax(): ?int
    {
        return null;
    }

    final public function __construct(?int $value)
    {
        $this->min = $this->getMin();
        $this->max = $this->getMax();
        if (!$this->isInRange($value)) {
            throw new OutOfRangeException(
                (new Message('Value %value% is out of the accepted range: %range%'))
                ->code('%value%', $value == null ? 'null' : (string) $value)
                ->code('%range%', implode(', ', $this->getAccept()))
            );
        }
        $this->value = $value;
    }

    final public function value(): ?int
    {
        return $this->value;
    }

    final public function getAccept(): array
    {
        return [$this->min, $this->max];
    }

    final public function isInRange(?int $int): bool
    {
        if ($this->min === null && $this->max === null) {
            return true;
        }
        if (isset($this->min, $this->max)) {
            if ($int === null) {
                return false;
            }

            return $this->checkMinMax($int);
        }
        if ($this->min === null) {
            return $this->checkMax($int);
        }

        return $this->checkMin($int);
    }

    private function checkMin(int $int): bool
    {
        return $this->min <= $int;
    }

    private function checkMax(int $int): bool
    {
        return $this->max >= $int;
    }

    private function checkMinMax(int $int): bool
    {
        return $this->checkMin($int) && $this->checkMax($int);
    }
}
