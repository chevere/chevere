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

use Chevere\Components\Message\Message;
use Chevere\Components\Var\VarStorable;
use Chevere\Exceptions\Router\RouteNotRoutableException;
use Chevere\Exceptions\Router\RouteWithoutEndpointsException;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\Route\RouteInterface;
use Throwable;

final class Routable implements RoutableInterface
{
    public function __construct(
        private RouteInterface $route
    ) {
        $this->assertExportable();
        $this->assertMethodControllers();
    }

    public function route(): RouteInterface
    {
        return $this->route;
    }

    /**
     * @throws RouteNotRoutableException
     */
    private function assertExportable(): void
    {
        try {
            $varStorable = new VarStorable($this->route);
            $varStorable->toExport();
        } catch (Throwable $e) {
            throw new RouteNotRoutableException(previous: $e);
        }
    }

    private function assertMethodControllers(): void
    {
        if ($this->route->endpoints()->count() === 0) {
            throw new RouteWithoutEndpointsException(
                (new Message("Instance of %className% doesn't contain any endpoint"))
                    ->code('%className%', get_class($this->route))
            );
        }
    }
}
