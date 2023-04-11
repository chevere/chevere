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

namespace Chevere\DataStructure\Traits;

use Chevere\DataStructure\Map;
use Iterator;

/**
 * @template-covariant TValue
 */
trait MapTrait
{
    /**
     * @var Map<TValue>
     */
    private Map $map;

    public function __construct()
    {
        $this->map = new Map();
    }

    public function keys(): array
    {
        return $this->map->keys();
    }

    public function count(): int
    {
        return $this->map->count();
    }

    public function getIterator(): Iterator
    {
        return $this->map->getIterator();
    }
}
