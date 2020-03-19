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

use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
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
        $this->specIndexMap = new SpecIndexMap;
    }

    public function withOffset(
        int $routeId,
        RouteEndpointSpec $routeEndpointSpec
    ): SpecIndexInterface {
        $new = clone $this;
        if ($new->specIndexMap->hasKey($routeId)) {
            $specMethods = $new->specIndexMap->get($routeId);
        } else {
            $specMethods = new SpecMethods;
            $new->specIndexMap = $new->specIndexMap->withPut($routeId, $specMethods);
        }
        $specMethods = $specMethods
            ->withPut(
                $routeEndpointSpec->key(),
                $routeEndpointSpec->jsonPath()
            );
        $new->specIndexMap = $new->specIndexMap->withPut($routeId, $specMethods);

        return $new;
    }

    public function specIndexMap(): SpecIndexMap
    {
        return deep_copy($this->specIndexMap);
    }

    public function has(int $id, string $methodName): bool
    {
        return $this->specIndexMap->hasKey($id)
            && $this->specIndexMap->get($id)->hasKey($methodName);
    }

    /**
     * @throws OutOfBoundsException if $id and $method doesn't match the index
     */
    public function get(int $id, string $methodName): string
    {
        return $this->specIndexMap->get($id)->get($methodName);
    }
}
