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

namespace Chevere\Components\Router\Interfaces;

use Chevere\Components\Common\Interfaces\ToArrayInterface;
use Chevere\Components\Route\Interfaces\RouteInterface;

interface RouterIndexInterface extends ToArrayInterface
{
    /**
     * Return an instance with the specified values.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified values.
     */
    public function withAdded(RouteInterface $route, string $group): RouterIndexInterface;

    public function has(string $routeName): bool;

    public function get(string $routeName): RouteIdentifierInterface;
}
