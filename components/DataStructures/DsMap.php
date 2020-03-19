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

namespace Chevere\Components\DataStructures;

use Ds\Map;

abstract class DsMap
{
    protected Map $map;

    final public function __construct(Map $map)
    {
        $this->map = $map;
    }

    final public function map(): Map
    {
        return $this->map;
    }
}
