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
use LogicException;
use InvalidArgumentException;
use Chevere\Components\Controllers\HeadController;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\MethodControllerNameCollection;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodControllerNameContract;
use Chevere\Contracts\Middleware\MiddlewareNameCollectionContract;
use Chevere\Contracts\Route\PathUriContract;
use Chevere\Contracts\Route\WildcardContract;
use Chevere\Contracts\Http\MethodControllerNameCollectionContract;
use Chevere\Contracts\Middleware\MiddlewareNameContract;
use Chevere\Contracts\Route\WildcardCollectionContract;

// IDEA: L10n support

final class Route implements RouteContract
{
    /** @var PathUriContract */
    private $pathUri;

    /** @var string Route name (if any, must be unique) */
    private $name;

    /** @var MiddlewareNameCollectionContract */
    private $middlewareNameCollection;

    /** @var WildcardCollectionContract */
    private $wildcardCollection;

    /** @var MethodControllerNameCollectionContract */
    private $methodControllerNameCollection;

    /** @var string Route path representation, with placeholder wildcards like /api/users/{0} */
    private $key;

    /** @var array An array containg details about the Route maker */
    private $maker;

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
        $this->middlewareNameCollection = new MiddlewareNameCollection();
        $this->methodControllerNameCollection = new MethodControllerNameCollection();
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
    public function key(): string
    {
        return $this->key;
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
    public function withName(string $name): RouteContract
    {
        if (!preg_match(RouteContract::REGEX_NAME, $name)) {
            throw new RouteInvalidNameException(
                (new Message('Expecting at least one alphanumeric, underscore, hypen or dot character, string %string% provided (regex %regex%)'))
                    ->code('%string%', $name)
                    ->code('%regex%', RouteContract::REGEX_NAME)
                    ->toString()
            );
        }
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
    public function name(): string
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
    public function withAddedMethodController(MethodControllerNameContract $methodController): RouteContract
    {
        if ($this->methodControllerNameCollection->has($methodController->method())) {
            throw new InvalidArgumentException(
                (new Message('Method %method% has been already registered'))
                    ->code('%method%', $methodController->method())->toString()
            );
        }
        $new = clone $this;
        $new->methodControllerNameCollection = $new->methodControllerNameCollection
            ->withAddedMethodControllerName($methodController);

        if (
            'GET' == $methodController->method()->toString()
            && $new->methodControllerNameCollection->has(new Method('HEAD'))) {
            $new = $new->withAddedMethodController(
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
    public function withAddedMiddlewareName(MiddlewareNameContract $middlewareName): RouteContract
    {
        $new = clone $this;
        $new->middlewareNameCollection = $new->middlewareNameCollection
            ->withAddedMiddlewareName($middlewareName);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function middlewareNameCollection(): MiddlewareNameCollectionContract
    {
        return $this->middlewareNameCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function controllerName(MethodContract $method): string
    {
        if (!$this->methodControllerNameCollection->has($method)) {
            throw new LogicException(
                (new Message('No controller is associated to HTTP method %method%'))
                    ->code('%method%', $method->toString())
                    ->toString()
            );
        }

        return $this->methodControllerNameCollection->get($method)
            ->controllerName()->toString();
    }

    private function handleSetWildcardCollection(): void
    {
        $pathUriWildcards = new PathUriWildcards($this->pathUri);
        $this->key = $pathUriWildcards->key();
        $this->wildcardCollection = new WildcardCollection();
        foreach ($pathUriWildcards->wildcards() as $wildcardName) {
            $this->wildcardCollection = $this->wildcardCollection
                ->withAddedWildcard(new Wildcard($wildcardName));
        }
    }

    private function handleSetRegex(): void
    {
        $regex = '^' . $this->key . '$';
        if (isset($this->wildcardCollection)) {
            foreach ($this->wildcardCollection as $key => $wildcard) {
                $regex = str_replace("{{$key}}", '(' . $wildcard->regex() . ')', $regex);
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
