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

use Chevere\Interfaces\DataInterface;
use ArrayIterator;

class Data implements DataInterface
{
    /** @var array */
    private $data;

    public function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->data = $data;
        }
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    public function count(): int
    {
        return count($this->data);
    }

    // TODO: Rename to 'set'
    public function setData(array $data): DataInterface
    {
        $this->data = $data;

        return $this;
    }

    // TODO: Rename to 'add'
    public function addData(array $data): DataInterface
    {
        if (null == $this->data) {
            $this->data = $data;
        } else {
            $this->data = array_replace_recursive($this->data, $data);
        }

        return $this;
    }

    // TODO: Rename to 'append'
    public function appendData($var): DataInterface
    {
        $this->data[] = $var;

        return $this;
    }

    // TODO: Rename to 'get'
    public function getData(): ?array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return $this->data ?? [];
    }

    // TODO: Rename to 'has'
    public function hasDataKey(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    // TODO: Rename to 'setKey'
    public function setDataKey(string $key, $var): DataInterface
    {
        $this->data[$key] = $var;

        return $this;
    }

    // TODO: Rename to 'getKey'
    public function getDataKey(string $key)
    {
        return $this->data[$key] ?? null;
    }

    // TODO: Rename to 'removeKey'
    public function removeDataKey(string $key): DataInterface
    {
        unset($this->data[$key]);

        return $this;
    }
}
