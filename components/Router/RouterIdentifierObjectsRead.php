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

use Chevere\Components\DataStructures\SplObjectStorageRead;
use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;

final class RouterIdentifierObjectsRead extends SplObjectStorageRead
{
    public function current(): RouteIdentifierInterface
    {
        return $this->objects->current();
    }

    /**
     * @return string Route path key
     */
    public function getInfo(): string
    {
        return $this->objects->getInfo();
    }
}
