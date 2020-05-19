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
use function DeepCopy\deep_copy;

trait DsMapTrait
{
    private Map $map;

    final public function __construct()
    {
        $this->map = new Map;
    }

    final public function keys(): array
    {
        return $this->map->keys()->toArray();
    }

    final public function count(): int
    {
        return $this->map->count();
    }

    final public function map(): Map
    {
        return deep_copy($this->map);
    }

    // public function put($key, $value): void;
    // public function withPut($key, $value): self;
    // public function hasKey($key): bool;
    // public function get($key);
}
