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

use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Interfaces\MethodControllerNameCollectionInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\MethodControllerNameCollection;
use Chevere\Components\Message\Message;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Components\Route\Interfaces\PathUriInterface;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;

final class Route implements RouteInterface
{
    /** @var string The named identifier */
    private RouteNameInterface $name;

    private PathUriInterface $pathUri;

    /** @var array An array containg details about the instance maker */
    private array $maker;

    private MiddlewareNameCollectionInterface $middlewareNameCollection;

    private MethodControllerNameCollectionInterface $methodControllerNameCollection;

    public function __construct(RouteNameInterface $name, PathUriInterface $pathUri)
    {
        $this->name = $name;
        $this->pathUri = $pathUri;
        $this->key = $this->pathUri->toString();
        $this->maker = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        $this->methodControllerNameCollection = new MethodControllerNameCollection();
    }

    public function pathUri(): PathUriInterface
    {
        return $this->pathUri;
    }

    public function maker(): array
    {
        return $this->maker;
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function withAddedMethodControllerName(MethodControllerNameInterface $methodControllerName): RouteInterface
    {
        $new = clone $this;
        $new->methodControllerNameCollection = $new->methodControllerNameCollection
            ->withAddedMethodControllerName($methodControllerName);

        return $new;
    }

    public function methodControllerNameCollection(): MethodControllerNameCollectionInterface
    {
        return $this->methodControllerNameCollection;
    }

    public function controllerName(MethodInterface $method): ControllerNameInterface
    {
        if (!$this->methodControllerNameCollection->has($method)) {
            throw new MethodNotFoundException(
                (new Message("Instance of %className% doesn't define a controller for HTTP method %method%"))
                    ->code('%className%', MethodControllerNameCollectionInterface::class)
                    ->code('%method%', $method::name())
                    ->toString()
            );
        }

        return $this->methodControllerNameCollection->get($method)
            ->controllerName();
    }

    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): RouteInterface
    {
        $new = clone $this;
        if (!isset($new->middlewareNameCollection)) {
            $new->middlewareNameCollection = new MiddlewareNameCollection();
        }
        $new->middlewareNameCollection = $new->middlewareNameCollection
            ->withAddedMiddlewareName($middlewareName);

        return $new;
    }

    public function hasMiddlewareNameCollection(): bool
    {
        return isset($this->middlewareNameCollection);
    }

    public function middlewareNameCollection(): MiddlewareNameCollectionInterface
    {
        return $this->middlewareNameCollection;
    }
}
