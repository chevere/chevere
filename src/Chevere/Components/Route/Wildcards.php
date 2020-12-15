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

use Chevere\Components\DataStructures\Traits\MapToArrayTrait;
use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Interfaces\Route\RouteWildcardInterface;
use Chevere\Interfaces\Route\WildcardsInterface;
use Ds\Map;
use RangeException;
use function DeepCopy\deep_copy;

final class Wildcards implements WildcardsInterface
{
    use MapTrait;
    use MapToArrayTrait;

    /** int => RouteWildcardInterface $route */
    private Map $map;

    /** name => int $pos */
    private Map $index;

    private int $pos = -1;

    public function __construct()
    {
        $this->map = new Map();
        $this->index = new Map();
    }

    public function __clone()
    {
        $this->map = deep_copy($this->map);
        $this->index = new Map($this->index->toArray());
    }

    public function withAddedWildcard(RouteWildcardInterface $routeWildcard): WildcardsInterface
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
            throw new RangeException(); // @codeCoverageIgnore
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
