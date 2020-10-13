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

namespace Chevere\Components\Attribute\Traits;

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;

trait CollectionTrait
{
    use MapTrait;

    public function contains(string $name): bool
    {
        return $this->map->hasKey($name);
    }

    private function withAssertAdd(object $object): object
    {
        $class = get_class($object);
        if ($this->contains($class)) {
            throw new OverflowException(
                (new Message('%name% has been already added'))
                    ->code('%name%', $class)
            );
        }
        $new = clone $this;
        $new->map->put($class, $object);

        return $new;
    }

    private function withAssertModify(object $object): object
    {
        $class = get_class($object);
        if (!$this->contains($class)) {
            throw new OutOfBoundsException(
                (new Message("%name% doesn't exists"))
                    ->code('%name%', $class)
            );
        }
        $new = clone $this;
        $new->map->put($class, $object);

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
