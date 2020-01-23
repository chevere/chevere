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

if (!function_exists('xdump') && !function_exists('xdd')) {
    /**
     * Dumps information about one or more variables.
     */
    function xdump(...$vars)
    {
        new Dumper(...$vars);
    }

    /**
     * Dumps information about one or more variables and die().
     */
    function xdd(...$vars)
    {
        new Dumper(...$vars);
        die(0);
    }
}
