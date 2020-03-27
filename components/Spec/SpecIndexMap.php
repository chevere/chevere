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

    public function put(string $name, SpecMethods $specMethods): void
    {
        /** @var \Ds\TKey $name */
        $this->map->put($name, $specMethods);
    }

    public function hasKey(string $name): bool
    {
        /** @var \Ds\TKey $name */
        return $this->map->hasKey($name);
    }

    public function get(string $name): SpecMethods
    {
        /**
         * @var \Ds\TKey $name
         * @var SpecMethods $return
         */
        $return = $this->map->get($name);

        return $return;
    }
}
