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

namespace Chevere\Components\Screen;

use Chevere\Components\Screen\Interfaces\ContainerInterface;
use Chevere\Components\Screen\Interfaces\ScreenContainerInterface;
use Chevere\Components\Screen\Interfaces\ScreenInterface;

/**
 * Read-only screen container.
 */
final class ScreenContainer implements ScreenContainerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function runtime(): ScreenInterface
    {
        return $this->container->get($this->container::RUNTIME);
    }

    public function debug(): ScreenInterface
    {
        return $this->container->get($this->container::DEBUG);
    }

    public function console(): ScreenInterface
    {
        return $this->container->get($this->container::CONSOLE);
    }

    public function get(string $name): ScreenInterface
    {
        return $this->container->get($name);
    }

    public function getAll(): array
    {
        return $this->container->getAll();
    }
}
