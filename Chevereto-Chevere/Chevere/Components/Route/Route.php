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
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodsContract;
use Chevere\Contracts\Route\RouteContract;

use const Chevere\APP_PATH;

// IDEA: L10n support
// FIXME: Use object properties

final class Route implements RouteContract
{
    /** @var string Route path passed, like /api/users/{user} */
    private $path;

    /** @var string Route name (if any, must be unique) */
    private $name;

    /** @var array Where clauses based on wildcards */
    private $wheres;

    /** @var array ['method' => 'controller',] */
    private $methods;

    /** @var array [MiddlewareContract,] */
    private $middlewares;

    /** @var array */
    private $wildcards;

    /** @var string Route path representation, with placeholder wildcards like /api/users/{0} */
    private $key;

    /** @var array An array containg details about the Route maker */
    private $maker;

    /** @var string */
    private $regex;

    /** @var string */
    private $type;

    public function __construct(string $path)
    {
        $pathUri = new PathUri($path);
        $this->path = $pathUri->path();
        $this->setMaker();
        if ($pathUri->hasHandlebars()) {
            $set = new Set($this->path);
            $this->key = $set->key();
            $this->wildcards = $set->toArray();
        } else {
            $this->key = $this->path;
        }
        $this->type = isset($this->wildcards) ? Route::TYPE_DYNAMIC : Route::TYPE_STATIC;
    }

    public function maker(): array
    {
        return $this->maker;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function hasName(): bool
    {
        return isset($this->name);
    }

    public function wheres(): array
    {
        return $this->wheres ?? [];
    }

    public function middlewares(): array
    {
        return $this->middlewares ?? [];
    }

    public function wildcardName(int $key): string
    {
        return $this->wildcards[$key] ?? '';
    }

    public function type(): string
    {
        return $this->type;
    }

    public function regex(): string
    {
        return $this->regex;
    }

    public function withName(string $name): RouteContract
    {
        if (!preg_match(RouteContract::REGEX_NAME, $name)) {
            throw new InvalidArgumentException(
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

    public function withWhere(string $wildcardName, string $regex): RouteContract
    {
        $new = clone $this;
        $wildcard = new Wildcard($wildcardName, $regex);
        $wildcard->bind($new);
        $new->wheres[$wildcardName] = $regex;

        return $new;
    }

    public function withAddedMethod(MethodContract $method): RouteContract
    {
        if (!$method->hasControllerName()) {
            throw new InvalidArgumentException(
                (new Message('Instance of %type% %argument% must include a controller name'))
                    ->code('%type%', MethodContract::class)
                    ->code('%argument%', '$method')
                    ->toString()
            );
        }
        if (isset($this->methods[$method->method()])) {
            throw new InvalidArgumentException(
                (new Message('Method %method% has been already registered'))
                    ->code('%method%', $method->method())->toString()
            );
        }
        $new = clone $this;
        $new->methods[$method->method()] = $method->controllerName();

        return $new;
    }

    public function withMethods(MethodsContract $methods): RouteContract
    {
        $new = clone $this;
        foreach ($methods as $method) {
            $new = $new->withAddedMethod($method);
        }

        return $new;
    }

    public function withAddedMiddleware(string $callable): RouteContract
    {
        $this->middlewares[] = $callable;

        return $this;
    }

    public function getController(string $httpMethod): string
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

    public function withFiller(): RouteContract
    {
        $new = clone $this;
        if (isset($new->wildcards)) {
            foreach ($new->wildcards as $v) {
                if (!isset($new->wheres[$v])) {
                    $new->wheres[$v] = RouteContract::REGEX_WILDCARD_WHERE;
                }
            }
        }
        if (isset($new->methods['GET']) && !isset($new->methods['HEAD'])) {
            $new = $new->withAddedMethod(
                (new Method('HEAD'))
                    ->withControllerName(HeadController::class)
            );
        }
        $new->regex = $new->getRegex($new->key ?? $new->path);

        return $new;
    }

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
        $this->maker['fileLine'] = $this->maker['file']. ':' . $this->maker['line'];
    }
}
