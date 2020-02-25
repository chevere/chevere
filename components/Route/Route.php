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

use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\MethodControllerNameCollection;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Route\Interfaces\PathUriInterface;
use Chevere\Components\Route\Interfaces\WildcardInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\WildcardCollectionInterface;

final class Route implements RouteInterface
{
    private PathUriInterface $pathUri;

    /** @var string Route path representation, with placeholder wildcards like /api/users/{0} */
    private string $key;

    /** @var array An array containg details about the Route maker */
    private array $maker;

    private string $regex;

    private WildcardCollectionInterface $wildcardCollection;

    private RouteNameInterface $name;

    private MiddlewareNameCollectionInterface $middlewareNameCollection;

    private MethodControllerNameCollectionInterface $methodControllerNameCollection;

    /**
     * Creates a new instance.
     */
    public function __construct(PathUriInterface $pathUri)
    {
        $this->pathUri = $pathUri;
        $this->key = $this->pathUri->toString();
        $this->setMaker();
        if ($this->pathUri->hasWildcards()) {
            $this->handleSetWildcardCollection();
        }
        $this->handleSetRegex();
    }

    public function pathUri(): PathUriInterface
    {
        return $this->pathUri;
    }

    public function maker(): array
    {
        return $this->maker;
    }

    public function regex(): string
    {
        return $this->regex;
    }

    public function withName(RouteNameInterface $name): RouteInterface
    {
        $new = clone $this;
        $new->name = $name;

        return $new;
    }

    public function hasName(): bool
    {
        return isset($this->name);
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function withAddedWildcard(WildcardInterface $wildcard): RouteInterface
    {
        $new = clone $this;
        $wildcard->assertPathUri(
            $new->pathUri()
        );
        $new->wildcardCollection = $new->wildcardCollection
            ->withAddedWildcard($wildcard);
        $new->handleSetRegex();

        return $new;
    }

    public function hasWildcardCollection(): bool
    {
        return isset($this->wildcardCollection);
    }

    public function wildcardCollection(): WildcardCollectionInterface
    {
        return $this->wildcardCollection;
    }

    public function withAddedMethod(MethodInterface $method, ControllerNameInterface $controllerName): RouteInterface
    {
        $new = clone $this;
        if (!isset($new->methodControllerNameCollection)) {
            $new->methodControllerNameCollection = new MethodControllerNameCollection();
        }
        $methodControllerName = new MethodControllerName($method, $controllerName);
        $new->methodControllerNameCollection = $new->methodControllerNameCollection
            ->withAddedMethodControllerName($methodControllerName);
        // $methodString = $methodControllerName->method()->toString();
        // if ('GET' == $methodString) {
        //     $new->methodControllerNameCollection = $new->methodControllerNameCollection
        //         ->withAddedMethodControllerName(
        //             new MethodControllerName(
        //                 new Method('HEAD'),
        //                 new ControllerName(HeadController::class)
        //             )
        //         );
        // }

        return $new;
    }

    public function hasMethodControllerNameCollection(): bool
    {
        return isset($this->methodControllerNameCollection);
    }

    public function methodControllerNameCollection(): MethodControllerNameCollectionInterface
    {
        return $this->methodControllerNameCollection;
    }

    public function controllerName(MethodInterface $method): ControllerNameInterface
    {
        if (!$this->hasMethodControllerNameCollection()) {
            throw new MethodNotFoundException(
                (new Message('Instance of %className% lacks of any %contract%'))
                    ->code('%className%', self::class)
                    ->code('%contract%', MethodControllerNameCollectionInterface::class)
                    ->toString()
            );
        }
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

    private function handleSetWildcardCollection(): void
    {
        $this->wildcardCollection = new WildcardCollection();
        foreach ($this->pathUri->wildcards() as $wildcardName) {
            $this->wildcardCollection = $this->wildcardCollection
                ->withAddedWildcard(new Wildcard($wildcardName));
        }
    }

    private function handleSetRegex(): void
    {
        $regex = '^' . $this->pathUri->key() . '$';
        if (isset($this->wildcardCollection)) {
            foreach ($this->wildcardCollection->toArray() as $key => $wildcard) {
                $regex = str_replace("{{$key}}", '(' . $wildcard->match()->toString() . ')', $regex);
            }
        }
        $this->regex = $regex;
    }

    private function setMaker(): void
    {
        $this->maker = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $this->maker['file'] = $this->maker['file'];
        $this->maker['fileLine'] = $this->maker['file'] . ':' . $this->maker['line'];
    }
}
