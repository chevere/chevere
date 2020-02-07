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

    public function withPrecision(int $precision): NumberInterface
    {
        if ($precision < 0) {
            throw new InvalidArgumentException(
                (new Message('Method %methodName% accepts only positive intergers and zero (0), integer %numberProvided% provided'))
                    ->code('%methodName%', __METHOD__)
                    ->code('%numberProvided%', (string) $precision)
                    ->toString()
            );
        }
        $new = clone $this;
        $new->precision = $precision;

        return $new;
    }

    public function precision(): int
    {
        return $this->precision;
    }

    public function toAbbreviate(): string
    {
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
            // 0 => '',
        ];
        foreach ($abbreviations as $exponent => $suffix) {
            $pow = pow(10, $exponent);
            if (abs($this->number) >= $pow) {
                $numberFormat = number_format($this->number / $pow, $this->precision);
                if ($this->precision > 0) {
                    $numberFormat = preg_replace('/\.0+$/', '', $numberFormat);
                }

                return $numberFormat . $suffix;
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
