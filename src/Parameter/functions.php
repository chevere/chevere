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

use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Regex\Regex;

/**
 * @param array<mixed, mixed> $default
*/
function arrayParameter(
    string $description = '',
    array $default = [],
): ArrayParameterInterface {
    $parameter = new ArrayParameter($description);

    return $parameter->withDefault($default);
}

function booleanParameter(
    string $description = '',
    bool $default = false,
): BooleanParameterInterface {
    $parameter = new BooleanParameter($description);

    return $parameter->withDefault($default);
}

function floatParameter(
    string $description = '',
    float $default = 0.0,
): FloatParameterInterface {
    $parameter = new FloatParameter($description);

    return $parameter->withDefault($default);
}

function integerParameter(
    string $description = '',
    int $default = 0,
): IntegerParameterInterface {
    $parameter = new IntegerParameter($description);

    return $parameter->withDefault($default);
}

function stringParameter(
    string $description = '',
    string $default = '',
    ?string $regex = null,
): StringParameterInterface {
    $parameter = new StringParameter($description);
    $parameter = $parameter->withDefault($default);
    if (isset($regex)) {
        $parameter = $parameter->withRegex(new Regex($regex));
    }

    return $parameter;
}

function objectParameter(
    string $className,
    string $description = '',
): ObjectParameterInterface {
    $parameter = new ObjectParameter($description);

    return $parameter->withClassName($className);
}

function parameters(
    ?ParameterInterface ...$required,
): ParametersInterface {
    $parameters = new Parameters();
    if ($required !== null) {
        /** @var ParameterInterface[] $required */
        $parameters = $parameters->withAdded(...$required);
    }

    return $parameters;
}
