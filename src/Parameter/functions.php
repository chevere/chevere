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

use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
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
use Throwable;

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
    if ($accept !== []) {
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
 * @param ParameterInterface $K Generic key parameter
 * @param ParameterInterface $V Generic value parameter
 */
function genericParameter(
    ParameterInterface $V,
    ?ParameterInterface $K = null,
    string $description = '',
): GenericParameterInterface {
    $parameter = new GenericParameter($description);
    if ($K !== null) {
        $parameter = $parameter->withKey($K);
    }

    return $parameter->withValue($V);
}

function parameters(
    ParameterInterface ...$required,
): ParametersInterface {
    return (new Parameters())->withAddedRequired(...$required);
}

function generic(
    ParameterInterface $V,
    ?ParameterInterface $K = null,
    string $description = '',
): ParametersInterface {
    $parameter = genericParameter($V, $K, $description);

    return new Generic($parameter);
}

/**
 * @param array<int|string, mixed> $arguments
 */
function arguments(
    ParametersInterface $parameters,
    array $arguments
): ArgumentsInterface {
    return new Arguments($parameters, $arguments);
}

function assertStringArgument(
    StringParameterInterface $parameter,
    string $argument,
): void {
    $regex = $parameter->regex();
    if ($regex->match($argument) === []) {
        throw new InvalidArgumentException(
            message("Argument value provided %provided% doesn't match the regex %regex%")
                ->withCode('%provided%', $argument)
                ->withCode('%regex%', strval($regex))
        );
    }
}

function assertIntegerArgument(
    IntegerParameterInterface $parameter,
    int $argument,
): void {
    $value = $parameter->accept();
    if ($value !== []) {
        if (in_array($argument, $value, true)) {
            return;
        }

        throw new InvalidArgumentException(
            message('Argument value provided %provided% is not an accepted value %value%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%value%', implode(',', $value))
        );
    }
    $minimum = $parameter->minimum() ?? PHP_INT_MIN;
    if ($argument < $minimum) {
        throw new InvalidArgumentException(
            message('Argument value provided %provided% is less than %minimum%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%minimum%', strval($minimum))
        );
    }
    $maximum = $parameter->maximum() ?? PHP_INT_MAX;
    if ($argument > $maximum) {
        throw new InvalidArgumentException(
            message('Argument value provided %provided% is greater than %maximum%')
                ->withCode('%provided%', strval($argument))
                ->withCode('%maximum%', strval($maximum))
        );
    }
}

/**
 * @param array<mixed, mixed> $argument
 */
function assertArrayArgument(
    ArrayParameterInterface $parameter,
    array $argument,
): void {
    foreach ($argument as $key => $value) {
        $key = strval($key);
        assertArgument($key, $parameter->parameters()->get($key), $value);
    }
}

/**
 * @param iterable<mixed, mixed> $argument
 */
function assertGenericArgument(
    GenericParameterInterface $parameter,
    iterable $argument,
): void {
    $generic = ' *generic';
    $genericKey = '_K' . $generic;
    $genericValue = '_V' . $generic;
    foreach ($argument as $key => $value) {
        assertArgument($genericKey, $parameter->key(), $key);
        assertArgument($genericValue, $parameter->value(), $value);
    }
}

function assertArgument(string $name, ParameterInterface $parameter, mixed $argument): void
{
    $parameters = parameters(
        ...[
            $name => $parameter,
        ]
    );
    $arguments = [
        $name => $argument,
    ];

    try {
        new Arguments($parameters, $arguments);
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            message('Argument [%name%]: %message%')
                ->withTranslate('%name%', $name)
                ->withTranslate('%message%', $e->getMessage())
        );
    }
}

function assertParameter(ParameterInterface $parameter, mixed $argument): void
{
    match (true) {
        $parameter instanceof StringParameterInterface
            // @phpstan-ignore-next-line
            => assertStringArgument($parameter, $argument),
        $parameter instanceof IntegerParameterInterface
            // @phpstan-ignore-next-line
            => assertIntegerArgument($parameter, $argument),
        $parameter instanceof ArrayParameterInterface
            // @phpstan-ignore-next-line
            => assertArrayArgument($parameter, $argument),
        $parameter instanceof GenericParameterInterface
            // @phpstan-ignore-next-line
            => assertGenericArgument($parameter, $argument),
        default => '',
    };
}
