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

namespace Chevere\Components\DataStructure;

use Chevere\Components\Message\Message;
use function Chevere\Components\Var\deepCopy;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\DataStructure\MapInterface;
use Ds\Map as DsMap;
use Generator;

final class Map implements MapInterface
{
    private DsMap $map;

    public function __construct(mixed ...$namedArguments)
    {
        $this->map = new DsMap();
        /** @var array $namedArguments */
        if ($namedArguments !== []) {
            $this->map->putAll($namedArguments);
        }
    }

    public function __clone()
    {
        $this->map = deepCopy($this->map);
    }

    public function keys(): array
    {
        return $this->map->keys()->toArray();
    }

    public function count(): int
    {
        return $this->map->count();
    }

    public function getGenerator(): Generator
    {
        /**
         * @var \Ds\Pair $pair
         */
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
                (new Message('Missing key(s) %keys%'))
                    ->code('%keys%', implode(', ', $missing))
            );
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $key)
    {
        try {
            return $this->map->get($key);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Key %key% not found'))->code('%key%', $key)
            );
        }
    }
}
