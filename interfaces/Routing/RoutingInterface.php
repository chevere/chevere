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

use Chevere\Interfaces\Router\RouterInterface;
use Chevere\Interfaces\Router\RouterMakerInterface;

interface RoutingInterface
{
    public function __construct(
        FsRoutesMakerInterface $routePathIterator,
        RouterMakerInterface $routerMaker
    );

    public function router(): RouterInterface;
}
