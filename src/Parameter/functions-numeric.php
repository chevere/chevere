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
use Chevere\Parameter\Interfaces\IntParameterInterface;
use InvalidArgumentException;
use function Chevere\Message\message;

/**
 * @param float[] $accept
 */
function float(
    string $description = '',
    ?float $default = null,
    ?float $min = null,
    ?float $max = null,
    array $accept = [],
): FloatParameterInterface {
    $parameter = new FloatParameter($description);
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }
    if ($min !== null) {
        $parameter = $parameter->withMin($min);
    }
    if ($max !== null) {
        $parameter = $parameter->withMax($max);
    }
    if ($accept !== []) {
        $parameter = $parameter->withAccept(...$accept);
    }

    return $parameter;
}

/**
 * @param int[] $accept
 */
function int(
    string $description = '',
    ?int $default = null,
    ?int $min = null,
    ?int $max = null,
    array $accept = [],
): IntParameterInterface {
    $parameter = new IntParameter($description);
    if ($accept !== []) {
        $parameter = $parameter->withAccept(...$accept);
    }
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }
    if ($min !== null) {
        $parameter = $parameter->withMin($min);
    }
    if ($max !== null) {
        $parameter = $parameter->withMax($max);
    }

    return $parameter;
}

function assertNumeric(
    IntParameterInterface|FloatParameterInterface $parameter,
    int|float $argument,
): int|float {
    $accept = $parameter->accept();
    if ($accept !== []) {
        if (in_array($argument, $accept, true)) {
            return $argument;
        }

        throw new InvalidArgumentException(
            (string) message(
                'Argument value provided `%provided%` is not an accepted value `%value%`',
                provided: strval($argument),
                value: implode(',', $accept)
            )
        );
    }
    $min = $parameter->min();
    if ($min !== null && $argument < $min) {
        throw new InvalidArgumentException(
            (string) message(
                'Argument value provided `%provided%` is less than `%min%`',
                provided: strval($argument),
                min: strval($min)
            )
        );
    }
    $max = $parameter->max();
    if ($max !== null && $argument > $max) {
        throw new InvalidArgumentException(
            (string) message(
                'Argument value provided `%provided%` is greater than `%max%`',
                provided: strval($argument),
                max: strval($max)
            )
        );
    }

    return $argument;
}

function assertInt(
    IntParameterInterface $parameter,
    int $argument,
): int {
    assertNumeric($parameter, $argument);

    return $argument;
}

function assertFloat(
    FloatParameterInterface $parameter,
    float $argument
): float {
    assertNumeric($parameter, $argument);

    return $argument;
}
