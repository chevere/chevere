<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Runtime\Contracts\Sets;

use Chevere\Components\Runtime\Contracts\SetContract;

interface SetDebugContract extends SetContract
{
    public function eeee(string $value);
}
