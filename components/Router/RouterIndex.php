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

namespace Chevere\Components\Router;

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use Ds\Map;
use InvalidArgumentException;

final class RouterIndex implements RouterIndexInterface
{
    private RouterIdentifiers $routerIdentifiers;

    /** @var Map <int>$id => <string>$key */
    private Map $index;

    public function __construct()
    {
        $this->routerIdentifiers = new RouterIdentifiers(new Map);
        $this->index = new Map;
    }

    public function withAdded(RouteableInterface $routeable, int $id, string $group): RouterIndexInterface
    {
        $new = clone $this;
        $key = $routeable->route()->path()->toString();
        $new->index = $new->index->copy();
        $new->index->put($id, $key);
        $new->routerIdentifiers->put(
            $key,
            new RouteIdentifier(
                $id,
                $group,
                $routeable->route()->name()->toString()
            )
        );

        return $new;
    }

    public function has(int $id): bool
    {
        return $this->index->hasKey($id);
    }

    public function hasKey(string $pathKey): bool
    {
        return $this->routerIdentifiers->hasKey($pathKey);
    }

    public function get(int $id): RouteIdentifierInterface
    {
        if (!$this->index->hasKey($id)) {
            throw new InvalidArgumentException(
                (new Message("Id %id% doesn't exists"))
                    ->code('%id%', (string) $id)
                    ->toString()
            );
        }

        return $this->routerIdentifiers->get($this->index->get($id));
    }

    public function toArray(): array
    {
        $array = [];
        /** @var RouteIdentifierInterface $routeIdentifier */
        foreach ($this->routerIdentifiers->map() as $pathKey => $routeIdentifier) {
            $array[$pathKey] = $routeIdentifier->toArray();
        }

        return $array;
    }
}
