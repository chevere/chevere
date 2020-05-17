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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Components\Spec\Specs\RoutableSpec;

final class RoutableSpecs
{
    use DsMapTrait;

    public function put(RoutableSpec $routableSpec): void
    {
        /** @var \Ds\TKey $key */
        $key = $routableSpec->key();
        $this->map->put($key, $routableSpec);
    }

    public function hasKey(string $key): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $key);
    }

    public function get(string $key): RoutableSpec
    {
        /**
         * @var \Ds\TKey $key
         * @var RoutableSpec $return
         */
        $return = $this->map->get($key);

        return $return;
    }
}
