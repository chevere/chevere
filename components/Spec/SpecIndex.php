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
use function DeepCopy\deep_copy;

/**
 * Maps route id (internal) to endpoint method spec paths.
 */
final class SpecIndex implements SpecIndexInterface
{
    private SpecIndexMap $specIndexMap;

    public function __construct()
    {
        $this->specIndexMap = new SpecIndexMap(new Map);
    }

    public function withOffset(
        int $id,
        RouteEndpointSpec $routeEndpointSpec
    ): SpecIndexInterface {
        $new = clone $this;
        $specMethods = new SpecMethods;
        if ($new->specIndexMap->hasKey($id)) {
            $specMethods = $new->specIndexMap->get($id);
        } else {
            $new->specIndexMap->put($id, $specMethods);
        }
        $specMethods = $specMethods
            ->withMethodJsonSpecPath(
                $routeEndpointSpec->method(),
                $routeEndpointSpec->jsonPath()
            );
        $new->specIndexMap->put($id, $specMethods);

        return $new;
    }

    public function specIndexMap(): SpecIndexMap
    {
        return deep_copy($this->specIndexMap);
    }

    /**
     * @throws OutOfBoundsException if $id and $method doesn't match the index
     */
    public function get(int $id, MethodInterface $method): string
    {
        return $this->specIndexMap->get($id)->get($method);
    }
}
