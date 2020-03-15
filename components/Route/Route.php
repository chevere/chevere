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

namespace Chevere\Components\Route;

use Chevere\Components\Http\Interfaces\MethodControllerInterface;
use Chevere\Components\Http\Interfaces\MethodControllersInterface;
use Chevere\Components\Http\MethodControllers;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;

final class Route implements RouteInterface
{
    /** @var string The named identifier */
    private RouteNameInterface $name;

    private RoutePathInterface $routePath;

    /** @var array An array containg details about the instance maker */
    private array $maker;

    private MiddlewareNameCollectionInterface $middlewareNameCollection;

    private MethodControllersInterface $methodControllers;

    public function __construct(RouteNameInterface $name, RoutePathInterface $routePath)
    {
        $this->name = $name;
        $this->routePath = $routePath;
        $this->key = $this->routePath->toString();
        $this->maker = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        $this->methodControllers = new MethodControllers;
        $this->middlewareNameCollection = new MiddlewareNameCollection;
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function path(): RoutePathInterface
    {
        return $this->routePath;
    }

    public function maker(): array
    {
        return $this->maker;
    }

    public function withAddedMethodController(MethodControllerInterface $methodController): RouteInterface
    {
        $new = clone $this;
        $new->methodControllers = $new->methodControllers
            ->withAddedMethodController(
                $methodController
            );

        return $new;
    }

    public function methodControllers(): MethodControllersInterface
    {
        return $this->methodControllers;
    }

    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): RouteInterface
    {
        $new = clone $this;
        $new->middlewareNameCollection = $new->middlewareNameCollection
            ->withAddedMiddlewareName($middlewareName);

        return $new;
    }

    public function middlewareNameCollection(): MiddlewareNameCollectionInterface
    {
        return $this->middlewareNameCollection;
    }
}
