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

final class RouteableSpecs
{
    use DsMapTrait;

    public function put(RouteableSpec $routeableSpec): void
    {
        /** @var \Ds\TKey */
        $key = $routeableSpec->key();
        $this->map->put($key, $routeableSpec);
    }

    public function hasKey(string $key): bool
    {
        /** @var \Ds\TKey $key */
        return $this->map->hasKey($key);
    }

    public function get(string $key): RouteableSpec
    {
        /**
         * @var RouteableSpec
         * @var \Ds\TKey $key
         */
        $return = $this->map->get($key);

        return $return;
    }
}
