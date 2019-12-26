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

use Chevere\Contracts\Data\DataContract;

/**
 * Data wrapper.
 */
class Data implements DataContract
{
    /** @var array */
    private array $data;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function withArray(array $data): DataContract
    {
        $new = clone $this;
        $new->data = $data;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withMergedArray(array $data): DataContract
    {
        if (isset($this->data)) {
            $data = array_merge_recursive($this->data, $data);
        }
        $new = clone $this;
        $new = $new->withArray($data);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAppend($var): DataContract
    {
        $new = clone $this;
        $new->data[] = $var;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedKey(string $key, $var): DataContract
    {
        $new = clone $this;
        $new->data[$key] = $var;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withRemovedKey(string $key): DataContract
    {
        $new = clone $this;
        unset($new->data[$key]);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function key(string $key)
    {
        return $this->data[$key] ?? null;
    }
}
