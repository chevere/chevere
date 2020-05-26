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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\ClassMap\ClassMappedException;
use Chevere\Exceptions\ClassMap\ClassNotExistsException;
use Chevere\Exceptions\ClassMap\ClassNotMappedException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use Ds\Map;

final class ClassMap implements ClassMapInterface
{
    /** @var Map [className => string] */
    private Map $classMap;

    /** @var Map [string => className] */
    private Map $flip;

    private bool $isStrict;

    public function __construct()
    {
        $this->classMap = new Map;
        $this->flip = new Map;
        $this->isStrict = true;
    }

    public function isStrict(): bool
    {
        return $this->isStrict;
    }

    public function withStrict(bool $isStrict): ClassMapInterface
    {
        $new = clone $this;
        $new->isStrict = $isStrict;

        return $new;
    }

    public function withPut(string $className, string $string): ClassMapInterface
    {
        if ($this->isStrict && !class_exists($className)) {
            throw new ClassNotExistsException(
                (new Message("Strict standards: Class name %className% doesn't exists"))
                    ->strong('%className%', $className)
            );
        }
        $known = $this->flip[$string] ?? null;
        if ($known && $known !== $className) {
            throw new ClassMappedException(
                (new Message('Attempting to map %className% to the same mapping of %known% -> %string%'))
                    ->code('%className%', $className)
                    ->code('%known%', $known)
                    ->code('%string%', $string)
            );
        }
        $new = clone $this;
        $new->classMap[$className] = $string;
        $new->flip[$string] = $className;

        return $new;
    }

    public function count(): int
    {
        return $this->classMap->count();
    }

    public function has(string $className): bool
    {
        return $this->classMap->hasKey($className);
    }

    public function get(string $className): string
    {
        if (!$this->has($className)) {
            throw new ClassNotMappedException(
                (new Message("Class %className% doesn't exists in the class map"))
                    ->code('%className%', $className)
            );
        }

        return $this->classMap[$className];
    }

    /**
     * @return array [className => string, ]
     */
    public function toArray(): array
    {
        return $this->classMap->toArray();
    }
}
