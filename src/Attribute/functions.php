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

use Attribute;
use Chevere\Attributes\Description;
use Chevere\Attributes\Regex;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * @param class-string<Attribute> $attribute
 * @phpstan-ignore-next-line
 */
function hasAttribute(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
    string $attribute
): bool {
    $attributes = $reflection->getAttributes($attribute);

    return $attributes !== [];
}

/**
 * @param class-string<Attribute> $attribute
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

// @phpstan-ignore-next-line
function getDescription(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
): Description {
    // @phpstan-ignore-next-line
    return getAttribute($reflection, Description::class);
}

// @phpstan-ignore-next-line
function getRegex(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
): Regex {
    // @phpstan-ignore-next-line
    return getAttribute($reflection, Regex::class);
}
