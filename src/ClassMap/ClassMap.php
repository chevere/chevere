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

namespace Chevere\ClassMap;

use Chevere\ClassMap\Interfaces\ClassMapInterface;
use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapToArrayTrait;
use Chevere\DataStructure\Traits\MapTrait;
use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\ClassNotExistsException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;

final class ClassMap implements ClassMapInterface
{
    /**
     * @template-use MapTrait<string>
     */
    use MapTrait;

    use MapToArrayTrait;

    /**
     * @var Map<string>
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
        if (! class_exists($className) && ! interface_exists($className)) {
            throw new ClassNotExistsException(
                message("Class name or interface %className% doesn't exists")
                    ->withStrong('%className%', $className)
            );
        }
        /** @var string $known */
        $known = $this->flip->has($key)
            ? $this->flip->get($key)
            : '';
        if ($known !== '' && $known !== $className) {
            throw new OverflowException(
                message('Attempting to map %className% to the same mapping of %known% -> %key%')
                    ->withCode('%className%', $className)
                    ->withCode('%known%', $known)
                    ->withCode('%key%', $key)
            );
        }
        $new = clone $this;
        $new->map = $new->map->withPut(...[
            $className => $key,
        ]);
        $new->flip = $new->flip->withPut(...[
            $key => $className,
        ]);

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
            /** @var string */
            return $this->map->get($className);
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                message("Class %className% doesn't exists in the class map")
                    ->withCode('%className%', $className)
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
            /** @var string */
            return $this->flip->get($key);
        } catch (OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                message("Key %key% doesn't map any class")
                    ->withCode('%key%', $key)
            );
        }
    }
}
