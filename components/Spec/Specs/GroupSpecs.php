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
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Spec\Specs\GroupSpecInterface;
use Chevere\Interfaces\Spec\Specs\GroupSpecsInterface;

final class GroupSpecs implements GroupSpecsInterface
{
    use DsMapTrait;

    public function withPut(GroupSpecInterface $groupSpec): GroupSpecsInterface
    {
        $new = clone $this;
        $new->map->put($groupSpec->key(), $groupSpec);

        return $new;
    }

    public function has(string $groupName): bool
    {
        return $this->map->hasKey($groupName);
    }

    public function get(string $groupName): GroupSpecInterface
    {
        /**
         * @var GroupSpecInterface $return
         */
        try {
            $return = $this->map->get($groupName);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(null, 0, $e);
        }

        return $return;
    }
}
