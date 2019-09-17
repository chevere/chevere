<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Route;

use LogicException;
use InvalidArgumentException;
use Chevere\Message\Message;
use Chevere\Path\Path;
use Chevere\Controllers\HeadController;
use Chevere\Utility\Str;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Http\MethodsContract;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Http\Method;

// IDEA: L10n support
// FIXME: Use object properties

final class Route implements RouteContract
{
    /** @const string Route without wildcards. */
    const TYPE_STATIC = 'static';

    /** @const string Route containing wildcards. */
    const TYPE_DYNAMIC = 'dynamic';

    /** @const string Regex pattern used by default (no explicit where). */
    const REGEX_WILDCARD_WHERE = '[A-z0-9\_\-\%]+';

    /** @const string Regex pattern used to detect {wildcard} and {wildcard?}. */
    const REGEX_WILDCARD_SEARCH = '/{([a-z\_][\w_]*\??)}/i';

    /** @const string Regex pattern used to validate route name. */
    const REGEX_NAME = '/^[\w\-\.]+$/i';

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

    public function __construct(string $path, string $controller = null)
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
        if (isset($controller)) {
            $this->setMethod(new Method('GET', $controller));
        }
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

    public function setName(string $name): RouteContract
    {
        // Validate $name
        if (!preg_match(static::REGEX_NAME, $name)) {
            throw new InvalidArgumentException(
                (new Message("Expecting at least one alphanumeric, underscore, hypen or dot character. String '%s' provided."))
                    ->code('%s', $name)
                    ->code('%p', static::REGEX_NAME)
                    ->toString()
            );
        }
        $this->name = $name;

        return $this;
    }

    public function setWhere(string $wildcardName, string $regex): RouteContract
    {
        $wildcard = new Wildcard($wildcardName, $regex);
        $wildcard->bind($this);
        $this->wheres[$wildcardName] = $regex;

        return $this;
    }

    public function setMethod(MethodContract $method): RouteContract
    {
        if (isset($this->methods[$method->method()])) {
            throw new InvalidArgumentException(
                (new Message('Method %s has been already registered.'))
                    ->code('%s', $method->method())->toString()
            );
        }
        $this->methods[$method->method()] = $method->controller();

        return $this;
    }

    public function setMethods(MethodsContract $methods): RouteContract
    {
        foreach ($methods as $method) {
            $this->setMethod($method);
        }

        return $this;
    }

    public function setId(string $id): RouteContract
    {
        $this->id = $id;

        return $this;
    }

    public function addMiddleware(string $callable): RouteContract
    {
        // $this->middlewares[] = $this->getCallableSome($callable);
        $this->middlewares[] = $callable;

        return $this;
    }

    public function getController(string $httpMethod): string
    {
        $controller = $this->methods[$httpMethod];
        if (!isset($controller)) {
            throw new LogicException(
                (new Message('No controller is associated to HTTP method %s.'))
                    ->code('%s', $httpMethod)
                    ->toString()
            );
        }

        return $controller;
    }

    public function fill(): RouteContract
    {
        if (isset($this->wildcards)) {
            foreach ($this->wildcards as $k => $v) {
                if (!isset($this->wheres[$v])) {
                    $this->wheres[$v] = static::REGEX_WILDCARD_WHERE;
                }
            }
        }
        if (isset($this->methods['GET']) && !isset($this->methods['HEAD'])) {
            $this->setMethod(new Method('HEAD', HeadController::class));
        }
        $this->regex = $this->getRegex($this->key ?? $this->path);

        return $this;
    }

    public function getRegex(string $pattern): string
    {
        $regex = '^' . $pattern . '$';
        if (!Str::contains('{', $regex)) {
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
        $maker['file'] = Path::relative($maker['file']);

        return $maker;
    }

    private function handleType(): void
    {
        if (!isset($this->key)) {
            $this->type = Route::TYPE_STATIC;
        } else {
            // Sets (optionals) are like /route/{0}
            $pregReplace = preg_replace('/{[0-9]+}/', '', $this->key);
            if (null != $pregReplace) {
                $pregReplace = trim(Path::normalize($pregReplace), '/');
            }
            $this->type = isset($pregReplace) ? Route::TYPE_DYNAMIC : Route::TYPE_STATIC;
        }
    }
}
