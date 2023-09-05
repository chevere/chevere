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
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
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
function integer(
    string $description = '',
    ?int $default = null,
    ?int $minimum = null,
    ?int $maximum = null,
    array $accept = [],
): IntegerParameterInterface {
    $parameter = new IntegerParameter($description);
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

function booleanInteger(
    string $description = '',
    ?int $default = null,
): IntegerParameterInterface {
    return integer(
        description: $description,
        default: $default,
        accept: [0, 1]
    );
}

function assertNumeric(
    IntegerParameterInterface|FloatParameterInterface $parameter,
    int|float $argument,
): int|float {
    $accept = $parameter->accept();
    if ($accept !== []) {
        if (in_array($argument, $accept, true)) {
            return $argument;
        }

        throw new InvalidArgumentException(
            message('Argument value provided %provided% is not an accepted value %value%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%value%', implode(',', $accept))
        );
    }
    $minimum = $parameter->minimum();
    if ($minimum !== null && $argument < $minimum) {
        throw new InvalidArgumentException(
            message('Argument value provided %provided% is less than %minimum%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%minimum%', strval($minimum))
        );
    }
    $maximum = $parameter->maximum();
    if ($maximum !== null && $argument > $maximum) {
        throw new InvalidArgumentException(
            message('Argument value provided %provided% is greater than %maximum%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%maximum%', strval($maximum))
        );
    }

    return $argument;
}

function assertInteger(
    IntegerParameterInterface $parameter,
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
