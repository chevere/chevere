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

namespace Chevere\Components\DataStructure\Traits;

use Chevere\Components\DataStructure\Map;
use Generator;

trait MapTrait
{
    private Map $map;

    public function __construct()
    {
        $this->map = new Map();
    }

    public function __clone()
    {
        $this->map = clone $this->map;
    }

    public function keys(): array
    {
        return $this->map->keys();
    }

    public function count(): int
    {
        return $this->map->count();
    }

    public function getGenerator(): Generator
    {
        return $this->map->getGenerator();
    }
}
