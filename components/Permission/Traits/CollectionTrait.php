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

namespace Chevere\Components\Permission\Traits;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Ds\Map;
use Generator;
use OverflowException;
use function DeepCopy\deep_copy;

trait CollectionTrait
{
    use DsMapTrait;

    public function contains(string $name): bool
    {
        return $this->map->hasKey($name);
    }

    private function withAssertAdd(object $object): object
    {
        if (!$this->contains($object::class)) {
            throw new OverflowException(
                (new Message('%name% has been already added'))
                    ->code('%name%', $object::class)
            );
        }
        $new = clone $this;
        $new->map->put($object::class, $object);

        return $new;
    }

    public function withAssertModify(object $object): object
    {
        if (!$this->contains($object::class)) {
            (new Message("%name% doesn't exists"))
                ->code('%name%', $object::class);
        }
        $new = clone $this;
        $new->map->put($object::class, $object);

        return $new;
    }

    private function assertGet(string $name)
    {
        try {
            return $this->map->get($name);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Name %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }
}
