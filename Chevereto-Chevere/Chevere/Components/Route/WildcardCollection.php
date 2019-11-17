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
        $new = clone $this;
        foreach ($wildcards as $wildcard) {
            $new = $new
                ->withAddedWildcard($wildcard);
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedWildcard(WildcardContract $wildcard): WildcardCollectionContract
    {
        $new = clone $this;
        $new->array[] = $wildcard;
        $new->index[] = $wildcard->name();

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
        $id = array_search($wildcard->name(), $this->index);

        return $this->array[$id];
    }

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
}
