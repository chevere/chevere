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

// IDEA: L10n support
// FIXME: Use object properties

final class Route implements RouteContract
{
    /** @var string Route id relative to the ArrayFile */
    private $id;

    /** @var string Route path like /api/users/{user} */
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

    /** @var string Route path representation with placeholder wildcards like /api/users/{0} */
    private $key;

    /** @var array Contains all the possible $set combinations when using optional wildcards */
    private $keyPowerSet;

    /** @var array An array containg details about the Route maker */
    private $maker;

    /** @var string */
    private $regex;

    /** @var string */
    private $type;

    public function __construct(string $path)
    {
        $pathValidate = new PathValidate($path);
        $this->path = $path;
        $this->maker = $this->getMakerData();
        if ($pathValidate->hasHandlebars()) {
            $set = new Set($this->path);
            $this->key = $set->key();
            $this->keyPowerSet = $set->keyPowerSet();
            $this->wildcards = $set->toArray();
        } else {
            $this->key = $this->path;
        }
        $this->handleType();
    }

    public function id(): string
    {
        return $this->id;
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

    public function keyPowerSet(): array
    {
        return $this->keyPowerSet ?? [];
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
        // Validate $name
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
        if (isset($this->methods[$method->method()])) {
            throw new InvalidArgumentException(
                (new Message('Method %s has been already registered.'))
                    ->code('%s', $method->method())->toString()
            );
        }
        $new = clone $this;
        $new->methods[$method->method()] = $method->controller();

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

    public function withId(string $id): RouteContract
    {
        $new = clone $this;
        $new->id = $id;

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

    public function fill(): RouteContract
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
                    ->withController(HeadController::class)
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

    private function getMakerData(): array
    {
        $maker = debug_backtrace(0, 3)[2];
        $maker['file'] = (new Path($maker['file']))->relative();

        return $maker;
    }

    private function handleType(): void
    {
        if (isset($this->key)) {
            // Sets (optionals) are like /route/{0}
            $pregReplace = preg_replace('/{[0-9]+}/', '', $this->key);
            if (null != $pregReplace) {
                $path = new Path($pregReplace);
                $pregReplace = trim($path->absolute(), '/');
            }
            $this->type = isset($pregReplace) ? Route::TYPE_DYNAMIC : Route::TYPE_STATIC;
            return;
        }
        $this->type = Route::TYPE_STATIC;
    }
}
