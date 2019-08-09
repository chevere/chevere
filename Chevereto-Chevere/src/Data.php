<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere;

use ArrayAccess;
use ArrayIterator;
use Chevere\Contracts\DataContract;

/**
 * Data wrapper.
 */
class Data implements DataContract
{
    /** @var array */
    private $data;

    public static function fromArrayAccess(ArrayAccess $data)
    {
        $that = new self();
        $that->data = $data;

        return $that;
    }

    public function __construct()
    {
        $this->data = [];
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function set(array $data): DataContract
    {
        $this->data = $data;

        return $this;
    }

    public function add(array $data): DataContract
    {
        if (null == $this->data) {
            $this->data = $data;
        } else {
            $this->data = array_replace_recursive($this->data, $data);
        }

        return $this;
    }

    public function append($var): DataContract
    {
        $this->data[] = $var;

        return $this;
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

    public function setKey(string $key, $var): DataContract
    {
        $this->data[$key] = $var;

        return $this;
    }

    public function getKey(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function removeKey(string $key): DataContract
    {
        unset($this->data[$key]);

        return $this;
    }
}
