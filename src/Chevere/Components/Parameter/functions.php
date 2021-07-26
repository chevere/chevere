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

namespace Chevere\Components\Parameter;

use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Parameter\ObjectParameterInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;

/**
 * @codeCoverageIgnore
 * @throws InvalidArgumentException
 */
function stringParameter(
    ?string $description,
    ?string $default,
    ?string $regex,
    string ...$attributes
): StringParameterInterface {
    $parameter = new StringParameter();
    if (isset($description)) {
        $parameter = $parameter->withDescription($description);
    }
    if (isset($default)) {
        $parameter = $parameter->withDefault($default);
    }
    if (isset($regex)) {
        $parameter = $parameter->withRegex(new Regex($regex));
    }
    if (isset($attributes)) {
        $parameter = $parameter->withAddedAttribute(...$attributes);
    }

    return $parameter;
}

/**
 * @codeCoverageIgnore
 */
function objectParameter(
    string $className,
    string $description = ''
): ObjectParameterInterface {
    return (new ObjectParameter($description))
        ->withClassName($className);
}
