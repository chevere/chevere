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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\Spec\RouteEndpointSpec;
use Ds\Map;
use function DeepCopy\deep_copy;

final class RouteEndpointSpecs
{
    private Map $map;

    public function __construct()
    {
        $this->map = new Map;
    }

    public function map(): Map
    {
        return $this->map;
    }

    public function withPut(RouteEndpointSpec $routeEndpointSpec): RouteEndpointSpecs
    {
        $new = clone $this;
        $new->map = deep_copy($new->map);
        $new->map->put($routeEndpointSpec->key(), $routeEndpointSpec);

        return $new;
    }

    public function hasKey(string $key): bool
    {
        return $this->map->hasKey($key);
    }

    public function get(string $key): RouteEndpointSpec
    {
        return $this->map->get($key);
    }
}
