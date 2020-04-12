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

namespace Chevere\Components\Router;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Components\Router\Interfaces\RouteableInterface;

final class Routeables
{
    use DsMapTrait;

    public function put(RouteableInterface $routeable): void
    {
        /** @var \Ds\TKey $key */
        $key = $routeable->route()->name()->toString();
        $this->map->put($key, $routeable);
    }

    public function hasKey(string $name): bool
    {
        /** @var \Ds\TKey $key */
        $key = $name;

        return $this->map->hasKey($key);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): RouteableInterface
    {
        /**
         * @var RouteableInterface $return
         * @var \Ds\TKey $name
         */
        $return = $this->map->get($name);

        return $return;
    }
}
