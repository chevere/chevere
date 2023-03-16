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
use Chevere\Parameter\Interfaces\NullParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use Chevere\Regex\Regex;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Throwable;

function arrayp(
    ParameterInterface ...$parameter
): ArrayParameterInterface {
    $array = new ArrayParameter();
    if ($parameter) {
        $array = $array->withAddedRequired(...$parameter);
    }

    return $array;
}

function booleanp(
    string $description = '',
    bool $default = false,
): BooleanParameterInterface {
    $parameter = new BooleanParameter($description);

    return $parameter->withDefault($default);
}

function nullp(
    string $description = '',
): NullParameterInterface {
    return new NullParameter($description);
}

function floatp(
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
function integerp(
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

function stringp(
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

function objectp(
    string $className,
    string $description = '',
): ObjectParameterInterface {
    $parameter = new ObjectParameter($description);

    return $parameter->withClassName($className);
}

function filep(
    string $description = '',
    ?StringParameterInterface $name = null,
    ?IntegerParameterInterface $size = null,
    ?StringParameterInterface $type = null,
): FileParameterInterface {
    return new FileParameter(
        name: $name ?? stringp(),
        size: $size ?? integerp(),
        type: $type ?? stringp(),
        description: $description,
    );
}

/**
 * @param ParameterInterface $V Generic value parameter
 * @param ParameterInterface|null $K Generic key parameter
 */
function genericp(
    ParameterInterface $V,
    ?ParameterInterface $K = null,
    string $description = '',
): GenericParameterInterface {
    if ($K === null) {
        $K = integerp();
    }

    return new GenericParameter($V, $K, $description);
}

function unionp(
    ParameterInterface ...$parameter
): UnionParameterInterface {
    $parameters = parameters(...$parameter);

    return new UnionParameter($parameters);
}

function parameters(
    ParameterInterface ...$required,
): ParametersInterface {
    return (new Parameters())->withAddedRequired(...$required);
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

function assertString(
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

function assertInteger(
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
 * @param array<int|string, mixed> $argument
 */
function assertArray(
    ArrayParameterInterface $parameter,
    array $argument,
): void {
    foreach ($argument as $key => $value) {
        $key = strval($key);
        assertNamedArgument($key, $parameter->parameters()->get($key), $value);
    }
}

/**
 * @param iterable<mixed, mixed> $argument
 */
function assertGeneric(
    GenericParameterInterface $parameter,
    iterable $argument,
): void {
    $generic = ' *generic';
    $genericKey = '_K' . $generic;
    $genericValue = '_V' . $generic;
    foreach ($argument as $key => $value) {
        assertNamedArgument($genericKey, $parameter->key(), $key);
        assertNamedArgument($genericValue, $parameter->value(), $value);
    }
}

function assertUnion(
    UnionParameterInterface $parameter,
    mixed $argument,
): void {
    $types = [];
    foreach ($parameter->parameters() as $parameter) {
        try {
            assertNamedArgument('', $parameter, $argument);

            return;
        } catch (Throwable $e) {
            $types[] = $parameter::class;
        }
    }

    throw new InvalidArgumentException(
        message("Argument provided doesn't match the union type %type%")
            ->withCode('%type%', implode(',', $types))
    );
}

function assertNamedArgument(string $name, ParameterInterface $parameter, mixed $argument): void
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
        arguments($parameters, $arguments);
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            message('Argument [%name%]: %message%')
                ->withTranslate('%name%', $name)
                ->withTranslate('%message%', $e->getMessage())
        );
    }
}

function assertArgument(ParameterInterface $parameter, mixed $argument): void
{
    match (true) {
        $parameter instanceof StringParameterInterface
        // @phpstan-ignore-next-line
        => assertString($parameter, $argument),
        $parameter instanceof IntegerParameterInterface
        // @phpstan-ignore-next-line
        => assertInteger($parameter, $argument),
        $parameter instanceof ArrayParameterInterface
        // @phpstan-ignore-next-line
        => assertArray($parameter, $argument),
        $parameter instanceof GenericParameterInterface
        // @phpstan-ignore-next-line
        => assertGeneric($parameter, $argument),
        $parameter instanceof UnionParameterInterface
        => assertUnion($parameter, $argument),
        default => '',
    };
}
