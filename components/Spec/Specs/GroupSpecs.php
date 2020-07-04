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
use Chevere\Interfaces\Spec\Specs\GroupSpecInterface;
use Chevere\Interfaces\Spec\Specs\GroupSpecsInterface;

final class GroupSpecs implements GroupSpecsInterface
{
    use DsMapTrait;

    public function put(GroupSpecInterface $groupSpec): void
    {
        $key = $groupSpec->key();
        $this->map->put($key, $groupSpec);
    }

    public function hasKey(string $key): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $key);
    }

    public function get(string $key): GroupSpecInterface
    {
        /**
         * @var GroupSpecInterface $return
         */
        $return = $this->map->get($key);

        return $return;
    }
}
