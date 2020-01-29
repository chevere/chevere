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

if (!function_exists('varInfo')) {
    /**
     * Returns dump information about one or more variables.
     */
    function varInfo(...$vars)
    {
        return (new VarDump(...$vars))->tostring();
    }
}

if (!function_exists('xdump')) {
    /**
     * Dumps information about one or more variables to the runtime screen.
     */
    function xdump(...$vars)
    {
        screens()->runtime()->addNl(
            (new VarDump(...$vars))
                ->toString()
        )->emit();
    }
}

if (!function_exists('xdd')) {
    /**
     * Dumps information about one or more variables to the runtime screen and die().
     */
    function xdd(...$vars)
    {
        screens()->runtime()->addNl(
            (new VarDump(...$vars))
                ->toString()
        )->emit();
        die(0);
    }
}
