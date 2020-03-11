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

use BadMethodCallException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;
use SplObjectStorage;

final class RouterIndex implements RouterIndexInterface
{
    private SplObjectStorage $objects;

    /** @var array key => id */
    private array $index;

    private int $count = -1;

    public function __construct()
    {
        $this->objects = new SplObjectStorage();
    }

    public function withAdded(RouteInterface $route, int $id, string $group): RouterIndexInterface
    {
        $new = clone $this;
        $new->count++;
        $new->index[$route->path()->toString()] = $this->count;
        $new->objects->attach(
            new RouteIdentifier($id, $group, $route->name()->toString()),
            $route->path()->toString()
        );

        return $new;
    }

    public function has(RoutePath $routePath): bool
    {
        return isset($this->index[$routePath->key()]);
    }

    public function get(RoutePath $routePath): RouteIdentifierInterface
    {
        if (!$this->has($routePath)) {
            throw new BadMethodCallException(
                (new Message("PathUri key %key% doesn't exists"))
                    ->code('%key%', $routePath->key())
                    ->toString()
            );
        }

        return $this->objects->offsetGet($this->index[$routePath->key()]);
    }

    public function toArray(): array
    {
        $array = [];
        $this->objects->rewind();
        while ($this->objects->valid()) {
            $array[$this->objects->getInfo()] = $this->objects->current()->toArray();
            $this->objects->next();
        }

        return $array;
    }
}
