<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Chevere\VarDump\Dumper;

// use Chevere\Console\Console;

/**
 * Dumps information about one or more variables.
 */
function dump(...$vars)
{
    $dumper = new Dumper();
    $dumper->dumper(...$vars);
}
/**
 * Dumps information about one or more variables and die().
 */
function dd(...$vars)
{
    $dumper = new Dumper();
    $dumper->dumper(...$vars);
    die(0);
}

// function console(): Console
// {
//     return new Console();
// }
