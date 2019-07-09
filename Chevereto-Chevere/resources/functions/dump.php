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

use Chevereto\Chevere\Dumper;

/**
 * Dumps information about one or more variables.
 */
function d(...$vars)
{
    Dumper::dump(...$vars);
}
/**
 * Dumps information about one or more variables and die().
 */
function dx(...$vars)
{
    Dumper::dx(...$vars);
}
