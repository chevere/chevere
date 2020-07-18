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

namespace Chevere\Components\Spec;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Components\Spec\Specs\RouteEndpointSpec;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Spec\SpecIndexInterface;
use Chevere\Interfaces\Spec\SpecIndexMapInterface;

final class SpecIndex implements SpecIndexInterface
{
    use DsMapTrait;

    private SpecIndexMapInterface $specIndexMap;

    public function withAddedRoute(string $routeName, RouteEndpointSpec $routeEndpointSpec): SpecIndexInterface
    {
        $new = clone $this;
        if ($new->map->hasKey($routeName)) {
            /** @var SpecEndpoints $specEndpoints */
            $specEndpoints = $new->map->get($routeName);
        } else {
            $specEndpoints = new SpecEndpoints;
            $new->map->put($routeName, $specEndpoints);
        }
        $specEndpoints = $specEndpoints->withPut($routeEndpointSpec);
        $new->map->put($routeName, $specEndpoints);

        return $new;
    }

    public function has(string $routeName, string $methodName): bool
    {
        if ($this->map->hasKey($routeName)) {
            /** @var SpecEndpoints $specEndpoints */
            $specEndpoints = $this->map->get($routeName);

            return $specEndpoints->hasKey($methodName);
        }

        return false;
    }

    public function get(string $routeName, string $methodName): string
    {
        /** @var SpecEndpoints $specEndpoints */
        try {
            $specEndpoints = $this->map->get($routeName);

            return $specEndpoints->get($methodName);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(null, 0, $e);
        }
    }
}
