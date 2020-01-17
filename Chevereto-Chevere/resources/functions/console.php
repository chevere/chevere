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

use Chevere\Components\App\Instances\ConsoleInstance;
use Chevere\Components\Console\Interfaces\ConsoleInterface;

function console(): ConsoleInterface
{
    return ConsoleInstance::get();
}
