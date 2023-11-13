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

use ArrayAccess;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\BoolParameterInterface;
use Chevere\Parameter\Interfaces\CastInterface;
use Chevere\Parameter\Interfaces\NullParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersAccessInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Iterator;
use ReflectionMethod;
use Throwable;
use function Chevere\Message\message;

function cast(mixed $argument): CastInterface
{
    return new Cast($argument);
}

function null(
    string $description = '',
): NullParameterInterface {
    return new NullParameter($description);
}

function object(
    string $className,
    string $description = '',
): ObjectParameterInterface {
    $parameter = new ObjectParameter($description);

    return $parameter->withClassName($className);
}

function union(
    ParameterInterface $one,
    ParameterInterface $two,
    ParameterInterface ...$more
): UnionParameterInterface {
    $parameters = parameters($one, $two, ...$more);

    return new UnionParameter($parameters);
}

function parameters(
    ParameterInterface ...$required,
): ParametersInterface {
    return new Parameters(...$required);
}

/**
 * @param array<int|string, mixed>|ArrayAccess<int|string, mixed> $arguments
 */
function arguments(
    ParametersInterface|ParametersAccessInterface $parameters,
    array|ArrayAccess $arguments
): ArgumentsInterface {
    $parameters = getParameters($parameters);

    return new Arguments($parameters, $arguments);
}

function assertBool(
    BoolParameterInterface $parameter,
    bool $argument
): bool {
    return $argument;
}

function assertNull(NullParameterInterface $parameter, mixed $argument): null
{
    if ($argument === null) {
        return $argument;
    }

    throw new TypeError(
        message('Argument value provided is not of type null')
    );
}

function assertObject(
    ObjectParameterInterface $parameter,
    object $argument
): object {
    if ($parameter->type()->validate($argument)) {
        return $argument;
    }

    throw new InvalidArgumentException(
        message('Argument value provided is not of type %type%')
            ->withCode('%type%', $parameter->className())
    );
}

function assertUnion(
    UnionParameterInterface $parameter,
    mixed $argument,
): mixed {
    $types = [];
    foreach ($parameter->parameters() as $item) {
        try {
            assertNamedArgument('', $item, $argument);

            return $argument;
        } catch (Throwable $e) {
            $types[] = $item::class;
        }
    }

    throw new InvalidArgumentException(
        message("Argument provided doesn't match the union type %type%")
            ->withCode('%type%', implode('|', $types))
    );
}

function assertNamedArgument(
    string $name,
    ParameterInterface $parameter,
    mixed $argument
): ArgumentsInterface {
    $parameters = parameters(
        ...[
            $name => $parameter,
        ]
    );
    $arguments = [
        $name => $argument,
    ];

    try {
        return arguments($parameters, $arguments);
    } catch (Throwable $e) {
        throw new InvalidArgumentException(
            message('Argument [%name%]: %message%')
                ->withTranslate('%name%', $name)
                ->withTranslate('%message%', $e->getMessage())
        );
    }
}

function assertArgument(ParameterInterface $parameter, mixed $argument): mixed
{
    return $parameter->__invoke($argument);
}

function methodParameters(string $class, string $method): ParametersInterface
{
    $parameters = parameters();
    $reflectionMethod = new ReflectionMethod($class, $method);
    foreach ($reflectionMethod->getParameters() as $reflection) {
        $typedReflection = new ReflectionParameterTyped($reflection);
        $callable = match ($reflection->isOptional()) {
            true => 'withOptional',
            default => 'withRequired',
        };
        $parameters = $parameters->{$callable}(
            $reflection->getName(),
            $typedReflection->parameter()
        );
    }

    return $parameters;
}

function arrayFrom(
    ParametersAccessInterface|ParametersInterface $parameter,
    string ...$name
): ArrayParameterInterface {
    return arrayp(
        ...takeFrom($parameter, ...$name)
    );
}

/**
 * @return array<string>
 */
function takeKeys(
    ParametersAccessInterface|ParametersInterface $parameter,
): array {
    return getParameters($parameter)->keys();
}

/**
 * @return Iterator<string, ParameterInterface>
 */
function takeFrom(
    ParametersAccessInterface|ParametersInterface $parameter,
    string ...$name
): Iterator {
    $parameters = getParameters($parameter);
    foreach ($name as $item) {
        yield $item => $parameters->get($item);
    }
}

function parametersFrom(
    ParametersAccessInterface|ParametersInterface $parameter,
    string ...$name
): ParametersInterface {
    $parameters = getParameters($parameter);

    return parameters(
        ...takeFrom($parameters, ...$name)
    );
}

function getParameters(
    ParametersAccessInterface|ParametersInterface $parameter
): ParametersInterface {
    return $parameter instanceof ParametersAccessInterface
        ? $parameter->parameters()
        : $parameter;
}
