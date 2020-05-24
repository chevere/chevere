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

namespace Chevere\Components\Route;

use Chevere\Interfaces\Route\RouteWildcardInterface;
use Chevere\Interfaces\Route\RouteWildcardsInterface;

final class RouteWildcards implements RouteWildcardsInterface
{
    /** @param array WildcardInterface[] */
    private array $array;

    /** @param array ['METHOD' => key,]*/
    private array $index;

    public function __construct()
    {
        $this->array = [];
        $this->index = [];
    }

    public function withAddedWildcard(RouteWildcardInterface $routeWildcard): RouteWildcardsInterface
    {
        $new = clone $this;
        $new->addWildcard($routeWildcard);

        return $new;
    }

    public function count(): int
    {
        return count($this->index);
    }

    public function hasAny(): bool
    {
        return $this->index !== [];
    }

    public function has(RouteWildcardInterface $routeWildcard): bool
    {
        return in_array($routeWildcard->name(), $this->index);
    }

    public function get(RouteWildcardInterface $routeWildcard): RouteWildcardInterface
    {
        $pos = array_search($routeWildcard->name(), $this->index);

        return $this->array[$pos];
    }

    public function hasPos(int $pos): bool
    {
        return isset($this->array[$pos]);
    }

    public function getPos(int $pos): RouteWildcardInterface
    {
        return $this->array[$pos];
    }

    public function toArray(): array
    {
        return $this->array;
    }

    private function addWildcard(RouteWildcardInterface $routeWildcard): void
    {
        $name = $routeWildcard->name();
        $pos = array_search($name, $this->index);
        if (false !== $pos) {
            $this->array[$pos] = $routeWildcard;
            $this->index[$pos] = $name;

            return;
        }
        $this->array[] = $routeWildcard;
        $this->index[] = $name;
    }
}
