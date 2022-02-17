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

function arrayParameter(
    ?string $description = null,
    ?array $default = null,
    string ...$attributes
): ArrayParameterInterface {
    $parameter = new ArrayParameter($description ?? '');
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
    $parameter = new BooleanParameter($description ?? '');
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
    $parameter = new FloatParameter($description ?? '');
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
    $parameter = new IntegerParameter($description ?? '');
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
    $parameter = new StringParameter($description ?? '');
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
    ?string $description = null,
): ObjectParameterInterface {
    $parameter = new ObjectParameter($description ?? '');

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
