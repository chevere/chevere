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
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Parameter\ObjectParameterInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;

/**
 * @throws InvalidArgumentException
 */
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

/**
 * @throws InvalidArgumentException
 */
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
