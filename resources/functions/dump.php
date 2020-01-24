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

use Chevere\Components\VarDump\Dumper;

if (!function_exists('varInfo')) {
    /**
     * Returns dump information about one or more variables.
     */
    function varInfo(...$vars)
    {
        return
            (new Dumper(...$vars))
                ->tostring();
    }
}

if (!function_exists('xdump')) {
    /**
     * Dumps information about one or more variables to the screen.
     */
    function xdump(...$vars)
    {
        (new Dumper(...$vars))
            ->toScreen();
    }
}

if (!function_exists('xdd')) {
    /**
     * Dumps information about one or more variables to the screen and die().
     */
    function xdd(...$vars)
    {
        (new Dumper(...$vars))
            ->toScreen();
        die(0);
    }
}
