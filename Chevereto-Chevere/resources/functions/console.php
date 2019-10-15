<?php

declare(strict_types=1);

use Chevere\Components\Console\Console;
use Chevere\Components\Console\Container;

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function console(): Console
{
    return Container::getInstance();
}
