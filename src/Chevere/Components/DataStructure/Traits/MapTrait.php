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

use function Chevere\Components\Var\deepCopy;
use Ds\Map;
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
        $this->map = new Map(deepCopy($this->map->toArray(), true));
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function keys(): array
    {
        return $this->map->keys()->toArray();
    }

    public function count(): int
    {
        return $this->map->count();
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     */
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
