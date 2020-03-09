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

final class RouteEndpointObjects extends SplObjectStorage
{
    public function current(): RouteEndpointInterface
    {
        return parent::current();
    }
}
