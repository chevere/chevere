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

namespace Chevere\Common;

use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

function assertClassName(string $interface, string $className): void
{
    (new ClassName($className))->assertInterface($interface);
}

/**
 * @see Symbol
 */
function getSymbolReflection(string $symbol): ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant
{
    $reflection = match (true) {
        class_exists($symbol) => new ReflectionClass($symbol),
        function_exists($symbol) => new ReflectionFunction($symbol),
        default => null,
    };
    if ($reflection) {
        return $reflection;
    }
    if (preg_match(Symbol::CLASS_METHOD, $symbol, $matches)) {
        $reflector = 'ReflectionMethod';
    } elseif (preg_match(Symbol::CLASS_PROPERTY, $symbol, $matches)) {
        $reflector = 'ReflectionProperty';
    } elseif (preg_match(Symbol::CLASS_METHOD_PARAMETER, $symbol, $matches)) {
        $reflector = 'ReflectionParameter';
        $matches[1] = [$matches[1], $matches[2]];
        $matches[2] = $matches[3];
    } elseif (preg_match(Symbol::FUNCTION_PARAMETER, $symbol, $matches)) {
        $reflector = 'ReflectionParameter';
    } elseif (preg_match(Symbol::CLASS_CONSTANT, $symbol, $matches)) {
        $reflector = 'ReflectionClassConstant';
    }
    $arguments = [$matches[1], $matches[2]];

    return new $reflector(...$arguments);
}
