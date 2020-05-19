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

use Chevere\Exceptions\ClassMap\ClassNotMappedException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use Chevere\Exceptions\Core\Exception;
use Chevere\Components\Message\Message;
use Ds\Map;

final class ClassMap implements ClassMapInterface
{
    /** @var Map [className => string] */
    private Map $classMap;

    /** @var Map [string => className] */
    private Map $flip;

    public function __construct()
    {
        $this->classMap = new Map;
        $this->flip = new Map;
    }

    public function withPut(string $className, string $string): ClassMapInterface
    {
        $known = $this->flip[$string] ?? null;
        if ($known && $known !== $className) {
            throw new Exception(
                (new Message('Attempting to map %className% to the same mapping of %known% -> %string%'))
                    ->code('%className%', $className)
                    ->code('%known%', $known)
                    ->code('%string%', $string)
            );
        }
        $new = clone $this;
        $new->classMap[$className] = $string;

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
     * @return array [classname => string, ]
     */
    public function toArray(): array
    {
        return $this->classMap->toArray();
    }
}
