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

/**
 * A type-hinted proxy for Ds\Map storing (int) routeId => [(string) methodName => (string) specJsonPath,]
 */
final class SpecIndexMap
{
    use DsMapTrait;

    public function put(string $routeName, SpecMethods $specMethods): void
    {
        $this->map->put($routeName, $specMethods);
    }

    public function hasKey(string $routeName): bool
    {
        return $this->map->hasKey($routeName);
    }

    public function get(string $routeName): SpecMethods
    {
        return $this->map->get($routeName);
    }
}
