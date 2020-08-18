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

namespace Chevere\Components\Route;

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Interfaces\Route\RouteWildcardInterface;
use Chevere\Interfaces\Route\RouteWildcardsInterface;
use Ds\Map;
use OutOfBoundsException;
use RangeException;
use function DeepCopy\deep_copy;

final class RouteWildcards implements RouteWildcardsInterface
{
    use MapTrait;

    /** @param Map [pos => RouteWildcard,]*/
    private Map $map;

    /** @param Map [wildcardName => pos,]*/
    private Map $index;

    private int $pos = -1;

    public function __construct()
    {
        $this->map = new Map;
        $this->index = new Map;
    }

    public function __clone()
    {
        $this->map = $this->mapCopy();
        $this->index = deep_copy($this->index);
    }

    public function withAddedWildcard(RouteWildcardInterface $routeWildcard): RouteWildcardsInterface
    {
        $new = clone $this;
        if ($new->index->hasKey($routeWildcard->toString())) {
            $new->pos = $new->index->get($routeWildcard->toString());
        } else {
            $new->pos++;
        }
        $new->index->put($routeWildcard->toString(), $new->pos);
        $new->map->put($new->pos, $routeWildcard);

        return $new;
    }

    public function has(string $wildcardName): bool
    {
        return $this->index->hasKey($wildcardName);
    }

    public function get(string $wildcardName): RouteWildcardInterface
    {
        $pos = $this->index->get($wildcardName);
        $get = $this->map->get($pos);
        if ($get === null) {
            throw new RangeException; // @codeCoverageIgnore
        }

        return $get;
    }

    public function hasPos(int $pos): bool
    {
        return $this->map->hasKey($pos);
    }

    public function getPos(int $pos): RouteWildcardInterface
    {
        return $this->map[$pos];
    }
}
