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

    public function keys(): array
    {
        return $this->keys;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function find(mixed $value): ?string
    {
        /** @var string|false $lookup */
        $lookup = array_search($value, $this->values, true);

        return $lookup === false ? null : $this->keys[$lookup];
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

    public function withPut(mixed ...$value): static
    {
        $new = clone $this;
        $new->put(...$value);

        return $new;
    }

    /**
     * @throws OutOfBoundsException
     */
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
     * @param TValue ...$value
     */
    public function contains(mixed ...$value): bool
    {
        try {
            $this->assertContains(...$value);

            return true;
        } catch (OutOfBoundsException) {
            return false;
        }
    }

    /**
     * @param TValue ...$value
     * @throws OutOfBoundsException
     */
    public function assertContains(mixed ...$value): void
    {
        $missing = [];
        foreach ($value as $name => $item) {
            if (array_search($item, $this->values, true) === false) {
                $missing[] = strval($name);
            }
        }
        if ($missing === []) {
            return;
        }

        throw new OutOfBoundsException(
            message('Missing value(s) %values%')
                ->withCode('%values%', implode(', ', $missing))
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
}
