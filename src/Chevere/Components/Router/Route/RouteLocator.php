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

namespace Chevere\Components\Router\Route;

use Chevere\Interfaces\Router\Route\RouteLocatorInterface;

final class RouteLocator implements RouteLocatorInterface
{
    private string $name;

    private string $repository;

    private string $path;

    public function __construct(string $repository, string $path)
    {
        $this->repository = $repository;
        $this->path = $path;
        $this->name = "${repository}:${path}";
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function repository(): string
    {
        return $this->repository;
    }

    public function path(): string
    {
        return $this->path;
    }
}
