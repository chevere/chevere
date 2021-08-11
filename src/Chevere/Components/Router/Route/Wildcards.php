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

namespace Chevere\Components\Router\Route;

use Chevere\Components\DataStructure\Map;
use Chevere\Components\DataStructure\Traits\MapToArrayTrait;
use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Interfaces\Router\Route\RouteWildcardInterface;
use Chevere\Interfaces\Router\Route\WildcardsInterface;
use RangeException;

final class Wildcards implements WildcardsInterface
{
    use MapTrait;

    use MapToArrayTrait;

    /**
     * int => RouteWildcardInterface $route
     */
    private Map $map;

    /**
     * name => int $pos
     */
    private Map $index;

    private int $pos = -1;

    public function __construct()
    {
        $this->map = new Map();
        $this->index = new Map();
    }

    public function __clone()
    {
        $this->map = clone $this->map;
        $this->index = clone $this->index;
    }

    public function withPut(RouteWildcardInterface $routeWildcard): WildcardsInterface
    {
        $new = clone $this;
        if ($new->index->has($routeWildcard->toString())) {
            $new->pos = $new->index->get($routeWildcard->toString());
        } else {
            $new->pos++;
        }
        $new->index = $new->index
            ->withPut($routeWildcard->toString(), $new->pos);
        $new->map = $new->map
            ->withPut(strval($new->pos), $routeWildcard);

        return $new;
    }

    public function has(string $wildcardName): bool
    {
        return $this->index->has($wildcardName);
    }

    public function get(string $wildcardName): RouteWildcardInterface
    {
        $pos = strval($this->index->get($wildcardName));
        $get = $this->map->get($pos);
        if ($get === null) {
            // @codeCoverageIgnoreStart
            throw new RangeException();
            // @codeCoverageIgnoreEnd
        }

        return $get;
    }

    public function hasPos(int $pos): bool
    {
        return $this->map->has(strval($pos));
    }

    public function getPos(int $pos): RouteWildcardInterface
    {
        return $this->map->get(strval($pos));
    }
}
