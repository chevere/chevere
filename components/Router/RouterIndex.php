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
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;

final class RouterIndex implements RouterIndexInterface
{
    private RouterIdentifierObjects $objects;

    /** @var array key => id */
    private array $index;

    private int $count = -1;

    public function __construct()
    {
        $this->objects = new RouterIdentifierObjects();
    }

    public function withAdded(RouteInterface $route, int $id, string $group): RouterIndexInterface
    {
        $new = clone $this;
        $new->count++;
        $new->index[$route->path()->toString()] = $new->count;
        $new->objects->append(
            new RouteIdentifier($id, $group, $route->name()->toString()),
            $route->path()->toString()
        );

        return $new;
    }

    public function has(string $key): bool
    {
        return isset($this->index[$key]);
    }

    public function get(string $key): RouteIdentifierInterface
    {
        if (!$this->has($key)) {
            throw new BadMethodCallException(
                (new Message("PathUri key %key% doesn't exists"))
                    ->code('%key%', $key)
                    ->toString()
            );
        }

        return $this->objects->offsetGet($this->index[$key]);
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
