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

use Chevere\Components\DataStructures\SplObjectStorageRead;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use SplObjectStorage;

final class RouteEndpointObjectsRead extends SplObjectStorageRead
{
    /**
     * @return RouteEndpointInterface
     */
    public function current(): RouteEndpointInterface
    {
        return $this->objects->current();
    }

    public function getInfo()
    {
        return $this->objects->getInfo();
    }
}
