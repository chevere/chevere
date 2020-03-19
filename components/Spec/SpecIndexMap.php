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

use Ds\Map;
use OutOfBoundsException;

/**
 * A type-hinted proxy for Ds\Map storing (int) routeId => [(string) methodName => (string) specJsonPath,]
 */
final class SpecIndexMap
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

    public function hasKey(int $id): bool
    {
        return $this->map->hasKey($id);
    }

    /**
     * @return SpecMethods
     * @throws OutOfBoundsException if $id not found
     */
    public function get(int $id): SpecMethods
    {
        return $this->map->get($id);
    }

    public function put(int $id, SpecMethods $specMethods): void
    {
        $this->map->put($id, $specMethods);
    }
}
