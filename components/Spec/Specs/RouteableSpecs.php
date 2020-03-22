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

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Components\Spec\RouteableSpec;
use Ds\Map;
use function DeepCopy\deep_copy;

final class RouteableSpecs
{
    use DsMapTrait;

    public function withPut(RouteableSpec $routeableSpec): RouteableSpecs
    {
        $new = clone $this;
        $new->map = deep_copy($new->map);
        $new->map->put($routeableSpec->key(), $routeableSpec);

        return $new;
    }

    public function hasKey(string $key): bool
    {
        return $this->map->hasKey($key);
    }

    public function get(string $key): RouteableSpec
    {
        return $this->map->get($key);
    }
}
