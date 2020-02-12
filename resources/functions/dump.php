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

use Chevere\Components\VarDump\VarDump;

if (function_exists('varInfo') === false) {
    /**
     * Returns dump information about one or more variables.
     */
    function varInfo(...$vars)
    {
        return '';// (new VarDump(...$vars))->toString();
    }
}

if (function_exists('xd') === false) {
    /**
     * Dumps information about one or more variables to the output stream
     */
    function xd(...$vars)
    {
        (new VarDump(writers()->out(), ...$vars))->withShift(1)->stream();
    }
}

if (function_exists('xdd') === false) {
    /**
     * Dumps information about one or more variables to the output stream and die().
     */
    function xdd(...$vars)
    {
        (new VarDump(writers()->out(), ...$vars))->withShift(1)->stream();
        die(0);
    }
}
