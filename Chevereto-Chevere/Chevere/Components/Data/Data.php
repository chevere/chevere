<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Data;

use ArrayIterator;

use Chevere\Contracts\DataContract;

/**
 * Data wrapper.
 */
class Data implements DataContract
{
    /** @var array */
    private $data;

    public function __construct()
    {
        $this->data = [];
    }

    public static function fromArray(array $data): DataContract
    {
        $that = new self();
        $that->data = $data;

        return $that;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function withArray(array $data): DataContract
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }

    public function withMergedArray(array $data): DataContract
    {
        if (isset($this->data)) {
            $data = array_merge_recursive($this->data, $data);
        }
        $new = clone $this;
        $new = $new->withArray($data);

        return $new;
    }

    public function withAppend($var): DataContract
    {
        $new = clone $this;
        $new->data[] = $var;

        return $new;
    }

    public function get(): ?array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return $this->data ?? [];
    }

    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function withKey(string $key, $var): DataContract
    {
        $new = clone $this;
        $new->data[$key] = $var;

        return $new;
    }

    public function key(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function removeKey(string $key): DataContract
    {
        unset($this->data[$key]);

        return $this;
    }
}
