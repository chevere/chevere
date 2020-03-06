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
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;

final class RouterIndex implements RouterIndexInterface
{
    private array $array = [];

    public function withAdded(RoutePathInterface $routePath, int $id, string $group, string $name): RouterIndexInterface
    {
        $new = clone $this;
        $new->array[$routePath->key()] = [
            'id' => $id,
            'group' => $group,
            'name' => $name,
        ];

        return $new;
    }

    public function has(RoutePath $routePath): bool
    {
        return isset($this->array[$routePath->key()]);
    }

    public function get(RoutePath $routePath): array
    {
        $get = $this->array[$routePath->key()] ?? null;
        if ($get === null) {
            throw new BadMethodCallException(
                (new Message("PathUri key %key% doesn't exists"))
                    ->code('%key%', $routePath->key())
                    ->toString()
            );
        }

        return $get;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
