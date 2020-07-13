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

use Chevere\Interfaces\Controller\ControllerNameInterface;
use Chevere\Interfaces\Router\RoutedInterface;

final class Routed implements RoutedInterface
{
    private ControllerNameInterface $controller;

    private array $arguments;

    public function __construct(ControllerNameInterface $controller, array $arguments)
    {
        $this->controller = $controller;
        $this->arguments = $arguments;
    }

    public function controllerName(): ControllerNameInterface
    {
        return $this->controller;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
