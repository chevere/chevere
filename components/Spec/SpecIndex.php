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
use Chevere\Components\Spec\Interfaces\SpecIndexInterface;
use OutOfBoundsException;
use function DeepCopy\deep_copy;

/**
 * Maps route name to endpoint method spec paths.
 */
final class SpecIndex implements SpecIndexInterface
{
    use DsMapTrait;

    private SpecIndexMap $specIndexMap;

    public function __construct()
    {
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
            $new->specIndexMap->put($routeName, $specMethods);
        }
        $specMethods->put(
            $routeEndpointSpec->key(),
            $routeEndpointSpec->jsonPath()
        );
        $new->specIndexMap->put($routeName, $specMethods);

        return $new;
    }

    public function specIndexMap(): SpecIndexMap
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
