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

    private array $wildcards;

    /**
     * Creates a new instance.
     */
    public function __construct(RouteInterface $route, array $wildcards)
    {
        $this->route = $route;
        $this->wildcards = $wildcards;
    }

    public function route(): RouteInterface
    {
        return $this->route;
    }

    public function wildcards(): array
    {
        return $this->wildcards;
    }
}
