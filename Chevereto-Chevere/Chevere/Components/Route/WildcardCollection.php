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

namespace Chevere\Components\Route;

use ArrayIterator;
use Chevere\Contracts\Route\WildcardCollectionContract;
use Chevere\Contracts\Route\WildcardContract;

final class WildcardCollection implements WildcardCollectionContract
{
    /** @param array WildcardContract[] */
    private $array;

    /** @param array ['METHOD' => key,]*/
    private $index;

    /**
     * {@inheritdoc}
     */
    public function __construct(WildcardContract ...$wildcards)
    {
        $this->array = [];
        $this->index = [];
        foreach ($wildcards as $wildcard) {
            $this->addWildcard($wildcard);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedWildcard(WildcardContract $wildcard): WildcardCollectionContract
    {
        $new = clone $this;
        $new->addWildcard($wildcard);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function has(WildcardContract $wildcard): bool
    {
        return in_array($wildcard->name(), $this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function get(WildcardContract $wildcard): WildcardContract
    {
        $pos = array_search($wildcard->name(), $this->index);

        return $this->array[$pos];
    }

    /**
     * {@inheritdoc}
     */
    public function hasPos(int $pos): bool
    {
        return isset($this->array[$pos]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPos(int $pos): WildcardContract
    {
        return $this->array[$pos];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->array);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }

    private function addWildcard(WildcardContract $wildcard)
    {
        $pos = array_search($wildcard->name(), $this->index);
        if (false !== $pos) {
            $this->array[$pos] = $wildcard;
            $this->index[$pos] = $wildcard->name();
        }
        $this->array[] = $wildcard;
        $this->index[] = $wildcard->name();
    }
}
