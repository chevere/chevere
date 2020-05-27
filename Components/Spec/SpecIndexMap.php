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
use Chevere\Interfaces\Spec\SpecIndexMapInterface;
use Chevere\Interfaces\Spec\SpecMethodsInterface;

/**
 * A type-hinted proxy for Ds\Map storing (int) routeId => [(string) methodName => (string) specJsonPath,]
 */
final class SpecIndexMap implements SpecIndexMapInterface
{
    use DsMapTrait;

    public function withPut(string $name, SpecMethodsInterface $specMethods): SpecIndexMapInterface
    {
        $new = clone $this;
        /** @var \Ds\TKey $name */
        $new->map->put($name, $specMethods);

        return $new;
    }

    public function hasKey(string $name): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $name);
    }

    public function get(string $name): SpecMethodsInterface
    {
        /**
         * @var \Ds\TKey $name
         * @var SpecMethodsInterface $return
         */
        $return = $this->map->get($name);

        return $return;
    }
}
