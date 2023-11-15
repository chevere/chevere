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
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use function Chevere\Message\message;

/**
 * @param float[] $accept
 */
function float(
    string $description = '',
    ?float $default = null,
    ?float $minimum = null,
    ?float $maximum = null,
    array $accept = [],
): FloatParameterInterface {
    $parameter = new FloatParameter($description);
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }
    if ($minimum !== null) {
        $parameter = $parameter->withMinimum($minimum);
    }
    if ($maximum !== null) {
        $parameter = $parameter->withMaximum($maximum);
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
    ?int $minimum = null,
    ?int $maximum = null,
    array $accept = [],
): IntParameterInterface {
    $parameter = new IntParameter($description);
    if ($accept !== []) {
        $parameter = $parameter->withAccept(...$accept);
    }
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }
    if ($minimum !== null) {
        $parameter = $parameter->withMinimum($minimum);
    }
    if ($maximum !== null) {
        $parameter = $parameter->withMaximum($maximum);
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
            message(
                'Argument value provided `%provided%` is not an accepted value `%value%`',
                provided: strval($argument),
                value: implode(',', $accept)
            )
        );
    }
    $minimum = $parameter->minimum();
    if ($minimum !== null && $argument < $minimum) {
        throw new InvalidArgumentException(
            message(
                'Argument value provided `%provided%` is less than `%minimum%`',
                provided: strval($argument),
                minimum: strval($minimum)
            )
        );
    }
    $maximum = $parameter->maximum();
    if ($maximum !== null && $argument > $maximum) {
        throw new InvalidArgumentException(
            message(
                'Argument value provided `%provided%` is greater than `%maximum%`',
                provided: strval($argument),
                maximum: strval($maximum)
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
