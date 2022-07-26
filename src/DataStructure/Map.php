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

namespace Chevere\DataStructure;

use Chevere\DataStructure\Interfaces\MapInterface;
use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use function Chevere\VariableSupport\deepCopy;
use Ds\Map as DsMap;
use Iterator;

final class Map implements MapInterface
{
    /**
     * @var DsMap<string, mixed>
     */
    private DsMap $map;

    public function __construct(mixed ...$namedArguments)
    {
        $this->map = new DsMap();
        if ($namedArguments !== []) {
            /** @var array<string, mixed> $namedArguments */
            $this->map->putAll($namedArguments);
        }
    }

    public function __clone()
    {
        /** @var DsMap<string, mixed> $copy */
        $copy = deepCopy($this->map);
        $this->map = $copy;
    }

    public function keys(): array
    {
        return $this->map->keys()->toArray();
    }

    public function count(): int
    {
        return $this->map->count();
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): Iterator
    {
        foreach ($this->map->pairs() as $pair) {
            yield $pair->key => $pair->value;
        }
    }

    public function withPut(string $key, mixed $value): self
    {
        $new = clone $this;
        $new->map->put($key, $value);

        return $new;
    }

    public function has(string ...$keys): bool
    {
        try {
            $this->assertHas(...$keys);

            return true;
        } catch (OutOfBoundsException) {
            return false;
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    public function assertHas(string ...$keys): void
    {
        $missing = [];
        foreach ($keys as $k) {
            if (!$this->map->hasKey($k)) {
                $missing[] = strval($k);
            }
        }
        if ($missing !== []) {
            throw new OutOfBoundsException(
                message('Missing key(s) %keys%')
                    ->withCode('%keys%', implode(', ', $missing))
            );
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $key): mixed
    {
        try {
            return $this->map->get($key);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                message('Key %key% not found')
                    ->withCode('%key%', $key)
            );
        }
    }
}
