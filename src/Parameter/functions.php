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
use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Regex\Regex;
use Chevere\Throwable\Exceptions\InvalidArgumentException;

function arrayParameter(
    ParameterInterface ...$parameter
): ArrayParameterInterface {
    $array = new ArrayParameter();
    if ($parameter) {
        $array = $array->withParameter(...$parameter);
    }

    return $array;
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

/**
 * @codeCoverageIgnore
 * @param int[] $accept
 */
function integerParameter(
    string $description = '',
    ?int $default = null,
    ?int $minimum = null,
    ?int $maximum = null,
    array $accept = [],
): IntegerParameterInterface {
    $parameter = new IntegerParameter($description);
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }
    if ($minimum !== null) {
        $parameter = $parameter->withMinimum($minimum);
    }
    if ($maximum !== null) {
        $parameter = $parameter->withMaximum($maximum);
    }
    if ($accept !== null) {
        $parameter = $parameter->withAccept(...$accept);
    }

    return $parameter;
}

function stringParameter(
    string $regex = '',
    string $description = '',
    ?string $default = null,
): StringParameterInterface {
    $parameter = new StringParameter($description);
    if ($default) {
        $parameter = $parameter->withDefault($default);
    }
    if ($regex !== '') {
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

/**
 * @param ParameterInterface $_K Generic key parameter
 * @param ParameterInterface $_V Generic value parameter
 */
function genericParameter(
    ParameterInterface $_K,
    ParameterInterface $_V,
    string $description = '',
): GenericParameterInterface {
    $parameter = new GenericParameter($description);

    return $parameter
        ->withKey($_K)
        ->withValue($_V);
}

function parameters(
    ParameterInterface ...$required,
): ParametersInterface {
    return (new Parameters())->withAddedRequired(...$required);
}

function fileParameter(
    string $description = '',
    ?StringParameterInterface $name = null,
    ?IntegerParameterInterface $size = null,
    ?StringParameterInterface $type = null,
): FileParameterInterface {
    return new FileParameter(
        name: $name ?? stringParameter(),
        size: $size ?? integerParameter(),
        type: $type ?? stringParameter(),
        description: $description,
    );
}

/**
 * @throws InvalidArgumentException
 */
function assertArgument(ParameterInterface $parameter, string $name, mixed $value): void
{
    $parameters = [
        $name => $parameter,
    ];
    $arguments = [
        $name => $value,
    ];
    new Arguments(
        parameters(...$parameters),
        ...$arguments
    );
}
