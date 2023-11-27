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
use Chevere\Parameter\Interfaces\ParameterAttributeInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersAccessInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use InvalidArgumentException;
use Iterator;
use LogicException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use Throwable;
use TypeError;
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

function assertNull(NullParameterInterface $parameter, mixed $argument): mixed
{
    if ($argument === null) {
        return $argument;
    }

    throw new TypeError(
        (string) message('Argument value provided is not of type null')
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
        (string) message(
            'Argument value provided is not of type `%type%`',
            type: $parameter->className()
        )
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
        (string) message(
            "Argument provided doesn't match the union type `%type%`",
            type: implode('|', $types)
        )
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
            (string) message(
                'Argument [%name%]: %message%',
                name: $name,
                message: $e->getMessage(),
            )
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

function getType(mixed $variable): string
{
    $type = \gettype($variable);

    return match ($type) {
        'integer' => 'int',
        'boolean' => 'bool',
        'double' => 'float',
        'NULL' => 'null',
        default => $type,
    };
}

/**
 * Retrieves a Parameter attribute instance from a function or method parameter.
 * @param array<string, string> $caller The result of debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]
 */
function parameterAttr(string $parameter, array $caller): ParameterAttributeInterface
{
    $class = $caller['class'] ?? null;
    $method = $caller['function'];
    $reflection = $class
        ? new ReflectionMethod($class, $method)
        : new ReflectionFunction($method);
    $parameters = $reflection->getParameters();
    foreach ($parameters as $parameterReflection) {
        if ($parameterReflection->getName() === $parameter) {
            return reflectedParameterAttribute($parameterReflection);
        }
    }

    throw new LogicException('No parameter attribute found');
}

function reflectedParameterAttribute(ReflectionParameter $reflection): ParameterAttributeInterface
{
    $attributes = $reflection->getAttributes();
    foreach ($attributes as $attribute) {
        $attribute = $attribute->newInstance();

        try {
            // @phpstan-ignore-next-line
            return $attribute;
        } catch (TypeError) { // @phpstan-ignore-line
        }
    }

    throw new LogicException('No parameter attribute found');
}
