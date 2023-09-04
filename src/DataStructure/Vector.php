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

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Iterator;
use function Chevere\Message\message;

final class Vector implements VectorInterface
{
    /**
     * @var array<mixed>
     */
    private array $values = [];

    private int $count = 0;

    public function __construct(mixed ...$value)
    {
        $this->put(...$value);
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function keys(): array
    {
        return array_keys($this->values);
    }

    public function count(): int
    {
        return $this->count;
    }

    #[\ReturnTypeWillChange]
    public function getIterator(): Iterator
    {
        foreach ($this->values as $value) {
            yield $value;
        }
    }

    public function withPush(mixed ...$value): self
    {
        $new = clone $this;
        $new->put(...$value);

        return $new;
    }

    public function withSet(int $key, mixed $value): self
    {
        $this->assertHas($key);
        $new = clone $this;
        $new->values[$key] = $value;

        return $new;
    }

    public function withUnshift(mixed ...$value): self
    {
        $new = clone $this;
        array_unshift($new->values, ...$value);
        $new->count += count($value);

        return $new;
    }

    public function withInsert(int $key, mixed ...$values): VectorInterface
    {
        $this->assertHas($key);
        $new = clone $this;
        array_splice($new->values, $key, 0, $values);
        $new->count += count($values);

        return $new;
    }

    public function withRemove(int ...$key): VectorInterface
    {
        $this->assertHas(...$key);
        $new = clone $this;
        foreach ($key as $item) {
            unset($new->values[$item]);
            $new->count--;
        }
        $new->values = array_values($new->values);

        return $new;
    }

    public function has(int ...$key): bool
    {
        try {
            $this->assertHas(...$key);

            return true;
        } catch (OutOfBoundsException) {
            return false;
        }
    }

    /**
     * @throws OutOfBoundsException
     */
    public function assertHas(int ...$key): void
    {
        $missing = [];
        foreach ($key as $item) {
            if (! $this->lookupKey($item)) {
                $missing[] = strval($item);
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
    public function get(int $key): mixed
    {
        if (! $this->lookupKey($key)) {
            throw new OutOfBoundsException(
                message('Key %key% not found')
                    ->withCode('%key%', strval($key))
            );
        }

        return $this->values[$key];
    }

    public function find(mixed $value): ?int
    {
        /** @var int|false $lookup */
        $lookup = array_search($value, $this->values, true);

        return $lookup === false ? null : $lookup;
    }

    public function contains(mixed ...$value): bool
    {
        foreach ($value as $item) {
            if ($this->find($item) === null) {
                return false;
            }
        }

        return true;
    }

    private function lookupKey(int $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    private function put(mixed ...$values): void
    {
        foreach ($values as $value) {
            $this->values[] = $value;
            $this->count++;
        }
    }
}
