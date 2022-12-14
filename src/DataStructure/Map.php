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
use Iterator;

final class Map implements MapInterface
{
    /**
     * @var array<mixed>
     */
    private array $values = [];

    /**
     * @var array<string|int>
     */
    private array $keys = [];

    private int $count = 0;

    public function __construct(mixed ...$value)
    {
        $this->put(...$value);
    }

    public function keys(): array
    {
        return $this->keys;
    }

    public function count(): int
    {
        return $this->count;
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): Iterator
    {
        foreach ($this->keys as $key) {
            /** @var string|int $lookup */
            $lookup = $this->lookupKey($key);
            yield $key => $this->values[$lookup];
        }
    }

    public function withPut(mixed ...$value): self
    {
        $new = clone $this;
        $new->put(...$value);

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
        foreach ($keys as $key) {
            if ($this->lookupKey($key) === false) {
                $missing[] = strval($key);
            }
        }
        if ($missing === []) {
            return;
        }

        throw new OutOfBoundsException(
            message('Missing key(s) %keys%')
                ->withCode('%keys%', implode(', ', $missing))
        );
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $key): mixed
    {
        $lookup = $this->lookupKey($key);
        if ($lookup === false) {
            throw new OutOfBoundsException(
                message('Key %key% not found')
                    ->withCode('%key%', $key)
            );
        }

        return $this->values[$lookup];
    }

    private function lookupKey(int|string $key): int|string|false
    {
        return array_search($key, $this->keys, true);
    }

    private function put(mixed ...$values): void
    {
        foreach ($values as $key => $value) {
            $lookUp = $this->lookupKey($key);
            if ($lookUp === false) {
                $this->keys[] = $key;
                $this->values[] = $value;
                $this->count++;

                continue;
            }
            $this->values[$lookUp] = $value;
        }
    }
}
