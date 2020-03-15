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
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;

final class RoutePathObjectsRead extends SplObjectStorageRead
{
    /**
     * @return RoutePathInterface
     */
    public function current(): RoutePathInterface
    {
        return $this->objects->current();
    }

    public function getInfo(): RouteDecoratorInterface
    {
        return $this->objects->getInfo();
    }
}
