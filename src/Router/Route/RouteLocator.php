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

namespace Chevere\Router\Route;

use Chevere\Router\Interfaces\Route\RouteLocatorInterface;

final class RouteLocator implements RouteLocatorInterface
{
    private string $name;

    public function __construct(
        private string $repository,
        private string $path
    ) {
        $this->name = "${repository}:${path}";
    }

    public function __toString(): string
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
