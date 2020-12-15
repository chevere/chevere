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

use Chevere\Interfaces\Action\ControllerInterface;
use Chevere\Interfaces\Action\ControllerNameInterface;
use Chevere\Interfaces\Router\RoutedInterface;

final class Routed implements RoutedInterface
{
    private ControllerNameInterface $controllerName;

    private array $arguments;

    public function __construct(ControllerNameInterface $controllerName, array $arguments)
    {
        $this->controllerName = $controllerName;
        $this->arguments = $arguments;
    }

    public function controllerName(): ControllerNameInterface
    {
        return $this->controllerName;
    }

    public function getController(): ControllerInterface
    {
        $controller = $this->controllerName->toString();

        return new $controller();
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
