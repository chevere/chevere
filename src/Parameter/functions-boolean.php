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

use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;

function boolean(
    string $description = '',
    ?bool $default = null,
): BooleanParameterInterface {
    $parameter = new BooleanParameter($description);
    if ($default !== null) {
        $parameter = $parameter->withDefault($default);
    }

    return $parameter;
}

function booleanString(
    string $description = '',
    ?string $default = null,
): StringParameterInterface {
    return string(
        regex: '/^[01]$/',
        description: $description,
        default: $default
    );
}

function booleanInteger(
    string $description = '',
    ?int $default = null,
): IntegerParameterInterface {
    return integer(
        description: $description,
        default: $default,
        accept: [0, 1]
    );
}
