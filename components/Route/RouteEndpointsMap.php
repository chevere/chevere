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

use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Countable;
use Ds\Map;

final class RouteEndpointsMap
{
    private Map $map;

    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    public function map(): Map
    {
        return $this->map;
    }

    public function get(string $methodName): RouteEndpointInterface
    {
        return $this->map->get($methodName);
    }
}
