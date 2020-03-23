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
    private string $routeName;

    private array $arguments;

    public function __construct(string $routeName, array $arguments)
    {
        $this->routeName = $routeName;
        $this->arguments = $arguments;
    }

    public function routeName(): string
    {
        return $this->routeName;
    }

    public function wildcards(): array
    {
        return $this->arguments;
    }
}
