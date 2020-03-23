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
        $this->map->put($routeable->route()->name()->toString(), $routeable);
    }

    public function hasKey(string $routeName): bool
    {
        return $this->map->hasKey($routeName);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $routeName): RouteableInterface
    {
        return $this->map->get($routeName);
    }
}
