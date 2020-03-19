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

use Chevere\Components\DataStructures\DsMap;
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;

final class RouterIdentifiers extends DsMap
{
    public function hasKey(string $pathKey): bool
    {
        return $this->map->hasKey($pathKey);
    }

    public function get(string $pathKey): RouteIdentifierInterface
    {
        return $this->map->get($pathKey);
    }

    public function put(string $pathKey, RouteIdentifierInterface $routeIdentifier): void
    {
        $this->map->put($pathKey, $routeIdentifier);
    }
}
