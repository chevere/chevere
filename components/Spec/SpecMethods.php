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
use Chevere\Interfaces\Spec\SpecMethodsInterface;

final class SpecMethods implements SpecMethodsInterface
{
    use DsMapTrait;

    public function withPut(string $name, string $jsonPath): SpecMethodsInterface
    {
        $new = clone $this;
        /** @var \Ds\TKey $name */
        $new->map->put($name, /** @scrutinizer ignore-type */ $jsonPath);

        return $new;
    }

    public function hasKey(string $name): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $name);
    }

    public function get(string $name): string
    {
        /**
         * @var \Ds\TKey $name
         * @var string $return
         */
        $return = $this->map->get($name);

        return $return;
    }
}
