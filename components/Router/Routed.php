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

use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Interfaces\RoutedInterface;

/**
 * An instance for routed RouteInterfaces routed by RouterInterface.
 */
final class Routed implements RoutedInterface
{
    private RouteInterface $route;

    private array $arguments;

    /**
     * Creates a new instance.
     */
    public function __construct(RouteInterface $route, array $arguments)
    {
        $this->route = $route;
        $this->arguments = $arguments;
    }

    public function route(): RouteInterface
    {
        return $this->route;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
