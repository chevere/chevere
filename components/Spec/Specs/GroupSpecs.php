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
use Chevere\Components\Message\Message;
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

    public function has(string $name): bool
    {
        return $this->map->hasKey($name);
    }

    public function get(string $name): GroupSpecInterface
    {
        /** @var GroupSpecInterface $return */
        try {
            $return = $this->map->get($name);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Group name %name% not found'))
                    ->code('%name%', $name)
            );
        }

        return $return;
    }
}
