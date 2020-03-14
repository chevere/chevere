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

use Chevere\Components\Router\Interfaces\RouteableInterface;
use SplObjectStorage;

// TODO: Read-only proxy
final class RouteableObjects extends SplObjectStorage
{
    public function append(RouteableInterface $routeable, int $id)
    {
        return parent::attach($routeable, $id);
    }

    public function current(): RouteableInterface
    {
        return parent::current();
    }

    public function getInfo(): int
    {
        return  parent::getInfo();
    }
}
