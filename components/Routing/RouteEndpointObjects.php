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

namespace Chevere\Components\Routing;

use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use SplObjectStorage;

// TODO: Read-only proxy
final class RouteEndpointObjects extends SplObjectStorage
{
    public function append(RouteEndpointInterface $routeEndpoint)
    {
        return parent::attach($routeEndpoint);
    }

    public function current(): RouteEndpointInterface
    {
        return parent::current();
    }
}
