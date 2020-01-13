<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Number;

use Chevere\Components\Message\Message;
use Chevere\Components\Number\Interfaces\NumberInterface;
use InvalidArgumentException;
use function ChevereFn\varType;

final class Number implements NumberInterface
{
    /** @var */
    private $number;

    /** @var int */
    private int $precision = 0;

    /**
     * Creates a new instance.
     */
    public function __construct($number)
    {
        $this->number = $number;
        $this->assertNumber();
    }

    /**
     * {@inheritdoc}
     */
    public function withPrecision(int $precision): NumberInterface
    {
        $new = clone $this;
        $new->precision = $precision;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function precision(): int
    {
        return $this->precision;
    }

    /**
     * {@inheritdoc}
     */
    public function toAbbreviate(): string
    {
        /** @var string */
        $string = (string) $this->number;
        if (0 == $this->number) {
            return $string;
        }
        $abbreviations = [
            24 => 'Y',
            21 => 'Z',
            18 => 'E',
            15 => 'P',
            12 => 'T',
            9 => 'B',
            6 => 'M',
            3 => 'K',
            0 => null,
        ];
        foreach ($abbreviations as $exponent => $abbreviation) {
            if ($this->number >= pow(10, $exponent)) {
                $div = $this->number / pow(10, $exponent);
                $float = floatval($div);
                $string = (string) (null === $abbreviation
                    ? $float
                    : (round($float, $this->precision) . $abbreviation));
                break;
            }
        }

        return $string;
    }

    private function assertNumber(): void
    {
        if (!is_int($this->number) && !is_float($this->number)) {
            throw new InvalidArgumentException(
                (new Message('Argument passed to %methodName% construct expects types %expected%, type %provided% provided'))
                    ->code('%methodName%', __CLASS__ . '::__construct')
                    ->code('%expected%', implode(', ', ['integer', 'float']))
                    ->code('%provided%', varType($this->number))
                    ->toString()
            );
        }
    }
}
