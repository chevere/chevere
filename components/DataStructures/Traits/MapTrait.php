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

namespace Chevere\Components\DataStructures\Traits;

use Ds\Map;
use Generator;
use function DeepCopy\deep_copy;

trait MapTrait
{
    private Map $map;

    public function __construct()
    {
        $this->map = new Map;
    }

    public function __clone()
    {
        $this->map = deep_copy($this->map);
    }

    public function keys(): array
    {
        return $this->map->keys()->toArray();
    }

    public function count(): int
    {
        return $this->map->count();
    }

    public function getGenerator(): Generator
    {
        /**
         * @var \Ds\Pair $pair
         */
        foreach ($this->map->pairs() as $pair) {
            yield $pair->key => $pair->value;
        }
    }
}
