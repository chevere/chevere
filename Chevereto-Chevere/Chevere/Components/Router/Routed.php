<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router;

use Chevere\Components\Route\Contracts\RouteContract;
use Chevere\Contracts\Router\RoutedContract;

/**
 * An instance for routed RouteContracts routed by RouterContract.
 */
final class Routed implements RoutedContract
{
    private RouteContract $route;

    private array $wildcards;

    /**
     * {@inheritdoc}
     */
    public function __construct(RouteContract $route, array $wildcards)
    {
        $this->route = $route;
        $this->wildcards = $wildcards;
    }

    /**
     * {@inheritdoc}
     */
    public function route(): RouteContract
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function wildcards(): array
    {
        return $this->wildcards;
    }
}
