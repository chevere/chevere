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
use InvalidArgumentException;
use SplObjectStorage;

final class RouterIndex implements RouterIndexInterface
{
    private SplObjectStorage $objects;

    /** @var array <string>$key => <int>$id */
    private array $array = [];

    /** @var array <int>$id => <string>$key */
    private array $index = [];

    private int $pos = -1;

    public function __construct()
    {
        $this->objects = new SplObjectStorage();
    }

    public function count(): int
    {
        return $this->pos + 1;
    }

    public function withAdded(RouteableInterface $routeable, int $id, string $group): RouterIndexInterface
    {
        $new = clone $this;
        $new->pos++;
        $key = $routeable->route()->path()->toString();
        $new->array[$key] = $new->pos;
        $new->index[$new->pos] = $key;
        $new->objects->attach(
            new RouteIdentifier($id, $group, $routeable->route()->name()->toString()),
            $routeable->route()->path()
        );

        return $new;
    }

    public function has(int $id): bool
    {
        return isset($this->index[$id]);
    }

    public function hasKey(string $key): bool
    {
        return isset($this->array[$key]);
    }

    public function idForKey(string $key): int
    {
        return $this->array[$key];
    }

    public function get(int $id): RouteIdentifierInterface
    {
        if (!$this->has($id)) {
            throw new InvalidArgumentException(
                (new Message("Id %id% doesn't exists"))
                    ->code('%id%', (string) $id)
                    ->toString()
            );
        }

        return $this->objects->offsetGet(
            $this->array[$this->index[$id]]
        );
    }

    public function objects(): RouterIdentifierObjectsRead
    {
        return new RouterIdentifierObjectsRead($this->objects);
    }

    public function toArray(): array
    {
        $array = [];
        $this->objects->rewind();
        while ($this->objects->valid()) {
            $array[$this->objects->getInfo()->toString()] = $this->objects->current()->toArray();
            $this->objects->next();
        }

        return $array;
    }
}
