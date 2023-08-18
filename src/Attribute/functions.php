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
use Chevere\Attributes\Enum;
use Chevere\Attributes\Regex;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

// @phpstan-ignore-next-line
function hasAttribute(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
    string $attribute
): bool {
    $attributes = $reflection->getAttributes($attribute);

    return $attributes !== [];
}

// @phpstan-ignore-next-line
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
 * @return array<int, object>
 * @phpstan-ignore-next-line
 */
function getAttributes(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
    string $attribute
): array {
    $attributes = $reflection->getAttributes($attribute);
    $return = [];
    if ($attributes === []) {
        return $return;
    }
    /**
     * @var ReflectionAttribute<Attribute> $attribute
     */
    foreach ($attributes as $attribute) {
        $return[] = $attribute->newInstance();
    }

    return $return;
}

// @phpstan-ignore-next-line
function descriptionAttribute(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
): Description {
    // @phpstan-ignore-next-line
    return getAttribute($reflection, Description::class);
}

// @phpstan-ignore-next-line
function regexAttribute(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
): Regex {
    // @phpstan-ignore-next-line
    return getAttribute($reflection, Regex::class);
}

// @phpstan-ignore-next-line
function enumAttribute(
    ReflectionClass|ReflectionFunction|ReflectionMethod|ReflectionProperty|ReflectionParameter|ReflectionClassConstant $reflection,
): Enum {
    // @phpstan-ignore-next-line
    return getAttribute($reflection, Enum::class);
}
