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
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecsInterface;
use function DeepCopy\deep_copy;

final class RouteEndpointSpecs implements RouteEndpointSpecsInterface
{
    use DsMapTrait;

    public function withPut(RouteEndpointSpecInterface $routeEndpointSpec): RouteEndpointSpecsInterface
    {
        $new = clone $this;
        $new->map = deep_copy($new->map);
        /** @var \Ds\TKey $key */
        $key = $routeEndpointSpec->key();
        $new->map->put($key, $routeEndpointSpec);

        return $new;
    }

    public function hasKey(string $key): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $key);
    }

    public function get(string $key): RouteEndpointSpecInterface
    {
        /**
         * @var \Ds\TKey $key
         * @var RouteEndpointSpec $return
         */
        $return = $this->map->get($key);

        return $return;
    }
}
