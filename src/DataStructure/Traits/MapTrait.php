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

use Chevere\DataStructure\Interfaces\MapInterface;
use Chevere\DataStructure\Map;
use function Chevere\VariableSupport\deepCopy;
use Iterator;

trait MapTrait
{
    private MapInterface $map;

    public function __construct()
    {
        $this->map = new Map();
    }

    public function __clone()
    {
        /** @var MapInterface $copy */
        $copy = deepCopy($this->map, true);
        $this->map = $copy;
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
