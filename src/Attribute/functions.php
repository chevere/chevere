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

namespace Chevere\Attribute;

use Chevere\Attributes\Description;
use Chevere\Common\Symbol;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use function Chevere\Common\getSymbolReflection;
use function Chevere\Message\message;

/**
 * @phpstan-ignore-next-line
 */
function getAttribute(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
    string $attribute
): object {
    $attributes = $reflection->getAttributes($attribute);
    if ($attributes === []) {
        return new $attribute();
    }

    return $attributes[0]->newInstance();
}

/**
 * @see Symbol
 */
function getDescription(string $symbol): Description
{
    // throw new InvalidArgumentException(
    //     message('Invalid symbol %symbol%')
    //         ->withCode('%symbol%', $symbol)
    // )

    $reflection = getSymbolReflection($symbol);
    /** @phpstan-ignore-next-line */
    return getAttribute($reflection, Description::class);
}
