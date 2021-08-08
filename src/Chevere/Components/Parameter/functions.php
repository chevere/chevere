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

namespace Chevere\Components\Parameter;

use Chevere\Components\Regex\Regex;
use Chevere\Interfaces\Parameter\ArrayParameterInterface;
use Chevere\Interfaces\Parameter\BooleanParameterInterface;
use Chevere\Interfaces\Parameter\FloatParameterInterface;
use Chevere\Interfaces\Parameter\IntegerParameterInterface;
use Chevere\Interfaces\Parameter\ObjectParameterInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;

function arrayParameter(
    ?string $description = null,
    ?array $default = null,
    string ...$attributes
): ArrayParameterInterface {
    $parameter = isset($description)
        ? new ArrayParameter($description)
        : new ArrayParameter();
    if (isset($default)) {
        $parameter = $parameter->withDefault($default);
    }
    if (isset($attributes)) {
        $parameter = $parameter->withAddedAttribute(...$attributes);
    }

    return $parameter;
}

function booleanParameter(
    ?string $description = null,
    ?bool $default = null,
    string ...$attributes
): BooleanParameterInterface {
    $parameter = isset($description)
        ? new BooleanParameter($description)
        : new BooleanParameter();
    if (isset($default)) {
        $parameter = $parameter->withDefault($default);
    }
    if (isset($attributes)) {
        $parameter = $parameter->withAddedAttribute(...$attributes);
    }

    return $parameter;
}

function floatParameter(
    ?string $description = null,
    ?float $default = null,
    string ...$attributes
): FloatParameterInterface {
    $parameter = isset($description)
        ? new FloatParameter($description)
        : new FloatParameter();
    if (isset($default)) {
        $parameter = $parameter->withDefault($default);
    }
    if (isset($attributes)) {
        $parameter = $parameter->withAddedAttribute(...$attributes);
    }

    return $parameter;
}

function integerParameter(
    ?string $description = null,
    ?int $default = null,
    string ...$attributes
): IntegerParameterInterface {
    $parameter = isset($description)
        ? new IntegerParameter($description)
        : new IntegerParameter();
    if (isset($default)) {
        $parameter = $parameter->withDefault($default);
    }
    if (isset($attributes)) {
        $parameter = $parameter->withAddedAttribute(...$attributes);
    }

    return $parameter;
}

function stringParameter(
    ?string $description = null,
    ?string $default = null,
    ?string $regex = null,
    string ...$attributes
): StringParameterInterface {
    $parameter = isset($description)
        ? new StringParameter($description)
        : new StringParameter();
    if (isset($default)) {
        $parameter = $parameter->withDefault($default);
    }
    if (isset($regex)) {
        $parameter = $parameter->withRegex(new Regex($regex));
    }
    if (isset($attributes)) {
        $parameter = $parameter->withAddedAttribute(...$attributes);
    }

    return $parameter;
}

function objectParameter(
    string $className,
    ?string $description = null
): ObjectParameterInterface {
    $parameter = isset($description)
        ? new ObjectParameter($description)
        : new ObjectParameter();

    return $parameter->withClassName($className);
}

function parameters(
    ?ParameterInterface ...$required,
): ParametersInterface {
    $parameters = new Parameters();
    if (isset($required)) {
        $parameters = $parameters->withAdded(...$required);
    }

    return $parameters;
}
