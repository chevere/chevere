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

namespace Chevere\Components\ClassMap;

use Chevere\Components\DataStructure\Map;
use Chevere\Components\DataStructure\Traits\MapToArrayTrait;
use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\ClassNotExistsException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;

final class ClassMap implements ClassMapInterface
{
    use MapTrait;

    use MapToArrayTrait;

    /**
     * @var Map [className => key]
     */
    private Map $map;

    /**
     * @var Map [key => className]
     */
    private Map $flip;

    public function __construct()
    {
        $this->map = new Map();
        $this->flip = new Map();
    }

    public function __clone()
    {
        $this->map = clone $this->map;
        $this->flip = clone $this->flip;
    }

    public function withPut(string $className, string $key): ClassMapInterface
    {
        if (!class_exists($className) && !interface_exists($className)) {
            throw new ClassNotExistsException(
                (new Message("Class name or interface %className% doesn't exists"))
                    ->strong('%className%', $className)
            );
        }
        $known = $this->flip->has($key)
            ? $this->flip->get($key)
            : null;
        if ($known && $known !== $className) {
            throw new OverflowException(
                (new Message('Attempting to map %className% to the same mapping of %known% -> %string%'))
                    ->code('%className%', $className)
                    ->code('%known%', $known)
                    ->code('%string%', $key)
            );
        }
        $new = clone $this;
        $new->map = $new->map->withPut($className, $key);
        $new->flip = $new->flip->withPut($key, $className);

        return $new;
    }

    public function has(string $className): bool
    {
        return $this->map->has($className);
    }

    public function hasKey(string $key): bool
    {
        return $this->flip->has($key);
    }

    public function key(string $className): string
    {
        try {
            return $this->map->get($className);
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message("Class %className% doesn't exists in the class map"))
                    ->code('%className%', $className)
            );
        }
    }

    public function keys(): array
    {
        return $this->flip->keys();
    }

    public function className(string $key): string
    {
        try {
            return $this->flip->get($key);
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message("Key %key% doesn't map any class"))
                    ->code('%key%', $key)
            );
        }
    }
}
