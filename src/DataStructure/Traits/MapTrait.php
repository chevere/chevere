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

namespace Chevere\DataStructure\Traits;

use Chevere\DataStructure\Map;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use function Chevere\VariableSupport\deepCopy;
use Iterator;

/**
 * @template TValue
 */
trait MapTrait
{
    /**
     * @var Map<TValue>
     */
    private Map $map;

    public function __construct()
    {
        $this->map = new Map();
    }

    public function __clone()
    {
        /** @var Map<TValue> $copy */
        $copy = deepCopy($this->map);
        // @phpstan-ignore-next-line
        $this->map = $copy;
    }

    public function keys(): array
    {
        return $this->map->keys();
    }

    public function count(): int
    {
        return $this->map->count();
    }

    public function has(string ...$key): bool
    {
        return $this->map->has(...$key);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function assertHas(string ...$key): void
    {
        $this->map->assertHas(...$key);
    }

    /**
     * @param TValue ...$value
     */
    public function contains(mixed ...$value): bool
    {
        return $this->map->contains(...$value);
    }

    /**
     * @param TValue ...$value
     */
    public function assertContains(mixed ...$value): void
    {
        $this->map->assertContains(...$value);
    }

    /**
     * @param TValue ...$value
     */
    public function find(mixed $value): ?string
    {
        return $this->map->find($value);
    }

    /**
     * @return TValue
     * @throws OutOfBoundsException
     */
    public function get(string $name): mixed
    {
        /** @var TValue */
        return $this->map->get($name);
    }

    /**
     * @return Iterator<string, TValue>
     */
    public function getIterator(): Iterator
    {
        return $this->map->getIterator();
    }
}
