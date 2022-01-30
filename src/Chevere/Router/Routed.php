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

namespace Chevere\Router;

use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Controller\Interfaces\ControllerNameInterface;
use Chevere\Router\Interfaces\RoutedInterface;

final class Routed implements RoutedInterface
{
    public function __construct(
        private ControllerNameInterface $controllerName,
        private array $arguments
    ) {
    }

    public function controllerName(): ControllerNameInterface
    {
        return $this->controllerName;
    }

    public function getController(): ControllerInterface
    {
        $controller = $this->controllerName->__toString();
        /** @var ControllerInterface */
        return new $controller();
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
