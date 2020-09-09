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

namespace Chevere\Components\Type;

function varType($var): string
{
    $type = strtolower(gettype($var));
    if ($type === 'double') {
        return 'float';
    }

    return $type;
}

function debugType($var): string
{
    $type = varType($var);
    if ($type === 'object') {
        return get_class($var);
    }

    return $type;
}
