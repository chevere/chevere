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

use Chevere\Components\Route\Interfaces\WildcardCollectionInterface;
use Chevere\Components\Route\Interfaces\WildcardInterface;

final class WildcardCollection implements WildcardCollectionInterface
{
    /** @param array WildcardContract[] */
    private array $array;

    /** @param array ['METHOD' => key,]*/
    private array $index;

    /**
     * Creates a new instance.
     */
    public function __construct(WildcardInterface ...$wildcards)
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
    public function withAddedWildcard(WildcardInterface $wildcard): WildcardCollectionInterface
    {
        $new = clone $this;
        $new->addWildcard($wildcard);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAny(): bool
    {
        return !empty($this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function has(WildcardInterface $wildcard): bool
    {
        return in_array($wildcard->name(), $this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function get(WildcardInterface $wildcard): WildcardInterface
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
    public function getPos(int $pos): WildcardInterface
    {
        return $this->array[$pos];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }

    private function addWildcard(WildcardInterface $wildcard)
    {
        $name = $wildcard->name();
        $pos = array_search($name, $this->index);
        if (false !== $pos) {
            $this->array[$pos] = $wildcard;
            $this->index[$pos] = $name;

            return;
        }
        $this->array[] = $wildcard;
        $this->index[] = $name;
    }
}
