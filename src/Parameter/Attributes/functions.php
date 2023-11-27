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

namespace Chevere\Parameter\Attributes;

use function Chevere\Parameter\parameterAttr;

function stringAttr(string $name): StringAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameterAttr($name, $caller);
}

function intAttr(string $name): IntAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameterAttr($name, $caller);
}

function floatAttr(string $name): FloatAttribute
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameterAttr($name, $caller);
}

function arrayAttr(string $name): ArrayAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameterAttr($name, $caller);
}

function genericAttr(string $name): GenericAttr
{
    $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];

    // @phpstan-ignore-next-line
    return parameterAttr($name, $caller);
}
