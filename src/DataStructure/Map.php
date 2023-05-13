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

/**
 * @template TValue
 * @implements MapInterface<TValue>
 */
final class Map implements MapInterface
{
    /**
     * @var array<mixed>
     */
    private array $values = [];

    /**
     * @var array<string>
     */
    private array $keys = [];

    private int $count = 0;

    public function __construct(mixed ...$value)
    {
        $this->put(...$value);
    }

    public function toArray(): array
    {
        return array_combine($this->keys, $this->values);
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
            /** @var string $lookup */
            $lookup = $this->lookupKey($key);
            yield $key => $this->values[$lookup];
        }
    }

    /**
     * @param TValue ...$value
     * @return self<TValue>
     */
    public function withPut(mixed ...$value): self
    {
        $new = clone $this;
        $new->put(...$value);

        return $new;
    }

    /**
     * @return self<TValue>
     */
    public function without(string ...$key): self
    {
        $new = clone $this;
        $new->out(...$key);

        return $new;
    }

    public function has(string ...$key): bool
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
    public function assertHas(string ...$key): void
    {
        $missing = [];
        foreach ($key as $item) {
            if ($this->lookupKey($item) === null) {
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
    public function get(string $key): mixed
    {
        $lookup = $this->lookupKey($key);
        if ($lookup === null) {
            throw new OutOfBoundsException(
                message('Key %key% not found')
                    ->withCode('%key%', $key)
            );
        }

        return $this->values[$lookup];
    }

    private function lookupKey(string $key): ?string
    {
        $lookup = array_search($key, $this->keys, true);

        return $lookup === false ? null : strval($lookup);
    }

    private function put(mixed ...$values): void
    {
        foreach ($values as $key => $value) {
            $key = strval($key);
            $lookUp = $this->lookupKey($key);
            if ($lookUp === null) {
                $this->keys[] = $key;
                $this->values[] = $value;
                $this->count++;

                continue;
            }
            $this->values[$lookUp] = $value;
        }
    }

    private function out(string ...$key): void
    {
        foreach ($key as $item) {
            $lookup = $this->lookupKey($item);
            if ($lookup === null) {
                throw new OutOfBoundsException(
                    message('Key %key% not found')
                        ->withCode('%key%', $item)
                );
            }
            unset($this->keys[$lookup], $this->values[$lookup]);
            $this->count--;
        }
        $this->keys = array_values($this->keys);
        // @infection-ignore-all
        $this->values = array_values($this->values);
    }
}
