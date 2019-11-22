<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Route;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controllers\HeadController;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\MethodControllerNameCollection;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Contracts\Controller\ControllerNameContract;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodControllerNameContract;
use Chevere\Contracts\Middleware\MiddlewareNameCollectionContract;
use Chevere\Contracts\Route\PathUriContract;
use Chevere\Contracts\Route\WildcardContract;
use Chevere\Contracts\Http\MethodControllerNameCollectionContract;
use Chevere\Contracts\Middleware\MiddlewareNameContract;
use Chevere\Contracts\Route\RouteNameContract;
use Chevere\Contracts\Route\WildcardCollectionContract;

final class Route implements RouteContract
{
    /** @var PathUriContract */
    private $pathUri;

    /** @var string Route path representation, with placeholder wildcards like /api/users/{0} */
    private $key;

    /** @var array An array containg details about the Route maker */
    private $maker;

    /** @var MiddlewareNameCollectionContract */
    private $middlewareNameCollection;

    /** @var WildcardCollectionContract */
    private $wildcardCollection;

    /** @var MethodControllerNameCollectionContract */
    private $methodControllerNameCollection;

    /** @var RouteNameContract Route name (if any) */
    private $name;

    /** @var string */
    private $regex;

    /**
     * {@inheritdoc}
     */
    public function __construct(PathUriContract $pathUri)
    {
        $this->pathUri = $pathUri;
        $this->key = $this->pathUri->path();
        $this->setMaker();
        if ($this->pathUri->hasWildcards()) {
            $this->handleSetWildcardCollection();
        }
        $this->handleSetRegex();
    }

    /**
     * {@inheritdoc}
     */
    public function pathUri(): PathUriContract
    {
        return $this->pathUri;
    }

    /**
     * {@inheritdoc}
     */
    public function maker(): array
    {
        return $this->maker;
    }

    /**
     * {@inheritdoc}
     */
    public function regex(): string
    {
        return $this->regex;
    }

    /**
     * {@inheritdoc}
     */
    public function withName(RouteNameContract $name): RouteContract
    {
        $new = clone $this;
        $new->name = $name;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasName(): bool
    {
        return isset($this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function name(): RouteNameContract
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedWildcard(WildcardContract $wildcard): RouteContract
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

    /**
     * {@inheritdoc}
     */
    public function hasWildcardCollection(): bool
    {
        return isset($this->wildcardCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function wildcardCollection(): WildcardCollectionContract
    {
        return $this->wildcardCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMethodControllerName(MethodControllerNameContract $methodControllerName): RouteContract
    {
        $new = clone $this;
        if (!isset($new->methodControllerNameCollection)) {
            $new->methodControllerNameCollection = new MethodControllerNameCollection();
        }
        $new->methodControllerNameCollection = $new->methodControllerNameCollection
            ->withAddedMethodControllerName($methodControllerName);

        if (
            'GET' == $methodControllerName->method()->toString()
            && $new->methodControllerNameCollection->has(new Method('HEAD'))) {
            $new = $new->withAddedMethodControllerName(
                new MethodControllerName(
                    new Method('HEAD'),
                    new ControllerName(HeadController::class)
                )
            );
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMethodControllerNameCollection(): bool
    {
        return isset($this->methodControllerNameCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function methodControllerNameCollection(): MethodControllerNameCollectionContract
    {
        return $this->methodControllerNameCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function controllerName(MethodContract $method): ControllerNameContract
    {
        if (!$this->hasMethodControllerNameCollection()) {
            throw new MethodNotFoundException(
                (new Message('Instance of %className% lacks of any %contract%'))
                    ->code('%className%', __CLASS__)
                    ->code('%contract%', MethodControllerNameCollectionContract::class)
                    ->toString()
            );
        }
        if (!$this->methodControllerNameCollection->has($method)) {
            throw new MethodNotFoundException(
                (new Message("Instance of %className% doesn't define a controller for HTTP method %method%"))
                    ->code('%className%', MethodControllerNameCollectionContract::class)
                    ->code('%method%', $method->toString())
                    ->toString()
            );
        }

        return $this->methodControllerNameCollection->get($method)
            ->controllerName();
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMiddlewareName(MiddlewareNameContract $middlewareName): RouteContract
    {
        $new = clone $this;
        if (!isset($new->middlewareNameCollection)) {
            $new->middlewareNameCollection = new MiddlewareNameCollection();
        }
        $new->middlewareNameCollection = $new->middlewareNameCollection
            ->withAddedMiddlewareName($middlewareName);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMiddlewareNameCollection(): bool
    {
        return isset($this->middlewareNameCollection);
    }

    /**
     * {@inheritdoc}
     */
    public function middlewareNameCollection(): MiddlewareNameCollectionContract
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
                $regex = str_replace("{{$key}}", '(' . $wildcard->regexMatch()->toString() . ')', $regex);
            }
        }
        $this->regex = $regex;
    }

    private function setMaker(): void
    {
        $this->maker = debug_backtrace(0, 2)[1];
        $this->maker['file'] = $this->maker['file'];
        $this->maker['fileLine'] = $this->maker['file'] . ':' . $this->maker['line'];
    }
}
