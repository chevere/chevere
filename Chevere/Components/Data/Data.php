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

namespace Chevere\Components\Data;

use Chevere\Components\Data\Interfaces\DataInterface;

/**
 * Data wrapper.
 */
class Data implements DataInterface
{
    /** @var array */
    private array $data;

    /**
     * Creates a new instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function withArray(array $data): DataInterface
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }

    public function withMergedArray(array $data): DataInterface
    {
        if (isset($this->data)) {
            $data = array_merge_recursive($this->data, $data);
        }
        $new = clone $this;

        return $new->withArray($data);
    }

    public function withAppend($var): DataInterface
    {
        $new = clone $this;
        $new->data[] = $var;

        return $new;
    }

    public function withAddedKey(string $key, $var): DataInterface
    {
        $new = clone $this;
        $new->data[$key] = $var;

        return $new;
    }

    public function withRemovedKey(string $key): DataInterface
    {
        $new = clone $this;
        unset($new->data[$key]);

        return $new;
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function key(string $key)
    {
        return $this->data[$key] ?? null;
    }
}
