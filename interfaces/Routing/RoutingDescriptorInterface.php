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

namespace Chevere\Interfaces\Routing;

use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Route\RouteDecoratorInterface;
use Chevere\Interfaces\Route\RoutePathInterface;

/**
 * Describes the component in charge of describing a route element.
 */
interface RoutingDescriptorInterface
{
    public function dir(): DirInterface;

    public function path(): RoutePathInterface;

    public function decorator(): RouteDecoratorInterface;
}
