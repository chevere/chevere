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

namespace Chevere\Components\Router\Routing;

use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\Route\RouteDecoratorInterface;
use Chevere\Interfaces\Router\Route\RoutePathInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorInterface;

/**
 * @codeCoverageIgnore
 */
final class RoutingDescriptor implements RoutingDescriptorInterface
{
    public function __construct(
        private DirInterface $dir,
        private RoutePathInterface $path,
        private RouteDecoratorInterface $decorator
    ) {
    }

    public function dir(): DirInterface
    {
        return $this->dir;
    }

    public function path(): RoutePathInterface
    {
        return $this->path;
    }

    public function decorator(): RouteDecoratorInterface
    {
        return $this->decorator;
    }
}
