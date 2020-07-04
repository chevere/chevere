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
use Chevere\Interfaces\Spec\SpecIndexInterface;
use Chevere\Interfaces\Spec\SpecIndexMapInterface;
use Ds\Map;
use OutOfBoundsException;
use function DeepCopy\deep_copy;

/**
 * Maps route name to endpoint method spec paths.
 */
final class SpecIndex implements SpecIndexInterface
{
    use DsMapTrait;

    private SpecIndexMapInterface $specIndexMap;

    public function __construct()
    {
        $this->map = new Map;
        $this->specIndexMap = new SpecIndexMap;
    }

    public function withOffset(
        string $routeName,
        RouteEndpointSpec $routeEndpointSpec
    ): SpecIndexInterface {
        $new = clone $this;
        if ($new->specIndexMap->hasKey($routeName)) {
            $specMethods = $new->specIndexMap->get($routeName);
        } else {
            $specMethods = new SpecMethods;
            $new->specIndexMap = $new->specIndexMap
                ->withPut($routeName, $specMethods);
        }
        $specMethods = $specMethods
            ->withPut(
                $routeEndpointSpec->key(),
                $routeEndpointSpec->jsonPath()
            );
        $new->specIndexMap = $new->specIndexMap
            ->withPut($routeName, $specMethods);

        return $new;
    }

    public function specIndexMap(): SpecIndexMapInterface
    {
        return deep_copy($this->specIndexMap);
    }

    public function has(string $routeName, string $methodName): bool
    {
        return $this->specIndexMap->hasKey($routeName)
            && $this->specIndexMap->get($routeName)->hasKey($methodName);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $routeName, string $methodName): string
    {
        return $this->specIndexMap->get($routeName)->get($methodName);
    }
}
