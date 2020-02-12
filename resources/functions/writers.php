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

use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Writers\Interfaces\WritersInterface;

function writers(): WritersInterface
{
    return WritersInstance::get();
}
