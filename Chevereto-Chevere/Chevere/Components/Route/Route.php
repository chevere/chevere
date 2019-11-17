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

use LogicException;
use InvalidArgumentException;
use Chevere\Components\Controllers\HeadController;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\MethodController;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Http\MethodsContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Components\Middleware\MiddlewareNames;
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Contracts\Http\MethodControllerContract;
use Chevere\Contracts\Middleware\MiddlewareNamesContract;
use Chevere\Contracts\Route\PathUriContract;
use Chevere\Contracts\Route\WildcardContract;

// IDEA: L10n support

final class Route implements RouteContract
{
    /** @var PathUriContract */
    private $pathUri;

    /** @var string Route name (if any, must be unique) */
    private $name;

    /** @var array Where clauses based on wildcards */
    private $wheres;

    /** @var array ['method' => 'controller',] */
    private $methods;

    /** @var MiddlewareNamesContract */
    private $middlewareNames;

    /** @var array */
    private $wildcards;

    /** @var string Route path representation, with placeholder wildcards like /api/users/{0} */
    private $key;

    /** @var array An array containg details about the Route maker */
    private $maker;

    /** @var string */
    private $regex;

    /** @var bool */
    private $isDynamic;

    /**
     * {@inheritdoc}
     */
    public function __construct(PathUriContract $pathUri)
    {
        $this->pathUri = $pathUri;
        $this->setMaker();
        if ($pathUri->hasWildcards()) {
            $pathUriWildcards = new PathUriWildcards($pathUri);
            $this->key = $pathUriWildcards->key();
            $this->wildcards = $pathUriWildcards->wildcards();
        } else {
            $this->key = $this->pathUri->path();
        }
        $this->isDynamic = isset($this->wildcards);
        $this->middlewareNames = new MiddlewareNames();
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
    public function isDynamic(): bool
    {
        return $this->isDynamic;
    }

    /**
     * {@inheritdoc}
     */
    public function wildcards(): array
    {
        return $this->wildcards;
    }

    /**
     * {@inheritdoc}
     */
    public function withName(string $name): RouteContract
    {
        if (!preg_match(RouteContract::REGEX_NAME, $name)) {
            throw new RouteInvalidNameException(
                (new Message("Expecting at least one alphanumeric, underscore, hypen or dot character. String '%s' provided."))
                    ->code('%s', $name)
                    ->code('%p', RouteContract::REGEX_NAME)
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
        $new->wheres[$wildcard->name()] = $wildcard->regex();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMethodController(MethodControllerContract $methodController): RouteContract
    {
        $new = clone $this;
        if (isset($this->methods[$methodController->method()->name()])) {
            throw new InvalidArgumentException(
                (new Message('Method %method% has been already registered'))
                    ->code('%method%', $methodController->method())->toString()
            );
        }
        $new = clone $this;
        $new->methods[$methodController->method()->name()] = $methodController->controllerName();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethods(MethodsContract $methods): RouteContract
    {
        $new = clone $this;
        foreach ($methods as $method) {
            $new = $new->withAddedMethod($method);
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMiddlewareName(string $middlewareName): RouteContract
    {
        $new = clone $this;
        $new->middlewareNames = $new->middlewareNames
            ->withAddedMiddlewareName($middlewareName);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withFiller(): RouteContract
    {
        $new = clone $this;
        if (isset($new->wildcards)) {
            foreach ($new->wildcards as $v) {
                if (!isset($new->wheres[$v])) {
                    $new->wheres[$v] = WildcardContract::REGEX_MATCH_DEFAULT;
                }
            }
        }
        if (isset($new->methods['GET']) && !isset($new->methods['HEAD'])) {
            $new = $new->withAddedMethodController(
                new MethodController(
                    new Method('HEAD'),
                    HeadController::class
                )
            );
        }
        $new->regex = $new->getRegex($new->key ?? $new->pathUri->path());

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function wheres(): array
    {
        return $this->wheres;
    }

    /**
     * {@inheritdoc}
     */
    public function middlewareNames(): MiddlewareNamesContract
    {
        return $this->middlewareNames;
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
    public function wildcardName(int $key): string
    {
        return $this->wildcards[$key] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function controller(string $httpMethod): string
    {
        $controller = $this->methods[$httpMethod] ?? null;
        if (!isset($controller)) {
            throw new LogicException(
                (new Message('No controller is associated to HTTP method %method%'))
                    ->code('%method%', $httpMethod)
                    ->toString()
            );
        }

        return $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegex(string $pattern): string
    {
        $regex = '^' . $pattern . '$';
        if (false === strpos($regex, '{')) {
            return $regex;
        }
        if (isset($this->wildcards)) {
            foreach ($this->wildcards as $k => $v) {
                $regex = str_replace("{{$k}}", '(' . $this->wheres[$v] . ')', $regex);
            }
        }

        return $regex;
    }

    private function setMaker(): void
    {
        $this->maker = debug_backtrace(0, 2)[1];
        $this->maker['file'] = $this->maker['file'];
        $this->maker['fileLine'] = $this->maker['file'] . ':' . $this->maker['line'];
    }
}
