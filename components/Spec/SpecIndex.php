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

use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
use Ds\Map;
use OutOfBoundsException;

/**
 * Maps route id (internal) to endpoint method spec paths.
 */
final class SpecIndex implements SpecIndexInterface
{
    private Map $map;

    public function __construct()
    {
        $this->map = new Map;
    }

    public function withOffset(
        int $id,
        RouteEndpointSpec $routeEndpointSpec
    ): SpecIndexInterface {
        $new = clone $this;
        $specMethods = new SpecMethods;
        if ($new->map->hasKey($id)) {
            $specMethods = $new->map->get($id);
        } else {
            $new->map->put($id, $specMethods);
        }
        $specMethods = $specMethods
            ->withMethodJsonSpecPath(
                $routeEndpointSpec->method(),
                $routeEndpointSpec->jsonPath()
            );
        $new->map->put($id, $specMethods);

        return $new;
    }

    public function specIndexMap(): SpecIndexMap
    {
        return new SpecIndexMap($this->map);
    }

    public function has(int $id, MethodInterface $method): bool
    {
        return $this->map->hasKey($id)
            && $this->map->get($id)->hasKey($method);
    }

    /**
     * @throws OutOfBoundsException if $id and $method doesn't match the index
     */
    public function get(int $id, MethodInterface $method): string
    {
        return $this->map->get($id)->get($method);
    }
}
