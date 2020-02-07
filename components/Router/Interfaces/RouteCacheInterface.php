<?php

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Route\Interfaces\RouteInterface;

interface RouteCacheInterface
{
    public function has(int $id): bool;

    public function get(int $id): RouteInterface;

    public function put(int $id, RouteableInterface $routeable): RouteCacheInterface;

    public function remove(int $id): RouteCacheInterface;

    public function puts(): array;
}
