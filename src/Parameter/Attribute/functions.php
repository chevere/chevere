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

namespace Chevere\Parameter\Attribute;

use Chevere\Parameter\Interfaces\ParameterAttributeInterface;
use LogicException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use TypeError;

/**
 * Retrieves a Parameter attribute instance from a function or method parameter.
 * @param array<string, string> $caller The result of debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]
 */
function parameter(string $parameter, array $caller): ParameterAttributeInterface
{
    $class = $caller['class'] ?? null;
    $method = $caller['function'];
    $reflection = $class
        ? new ReflectionMethod($class, $method)
        : new ReflectionFunction($method);
    $parameters = $reflection->getParameters();
    foreach ($parameters as $parameterReflection) {
        if ($parameterReflection->getName() === $parameter) {
            return reflectedParameter($parameterReflection);
        }
    }

    throw new LogicException('No parameter attribute found');
}

function reflectedParameter(ReflectionParameter $reflection): ParameterAttributeInterface
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

function stringAttr(string $name): StringAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameter($name, $caller);
}

function intAttr(string $name): IntAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameter($name, $caller);
}

function floatAttr(string $name): FloatAttribute
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameter($name, $caller);
}

function arrayAttr(string $name): ArrayAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameter($name, $caller);
}

function genericAttr(string $name): GenericAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameter($name, $caller);
}
