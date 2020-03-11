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

use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;
use SplObjectStorage;

final class RouterIdentifierObjects extends SplObjectStorage
{
    public function attach(RouteIdentifierInterface $routeIdentifier)
    {
        return parent::attach($routeIdentifier);
    }

    public function current(): RouteIdentifierInterface
    {
        return parent::current();
    }
}
