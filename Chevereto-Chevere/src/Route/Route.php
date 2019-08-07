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
use Chevere\Message;
use Chevere\Path;
use Chevere\Route\src\KeyValidation;
use Chevere\Route\src\Wildcards;
use Chevere\Route\src\WildcardValidation;
use Chevere\Controllers\HeadController;
use Chevere\Interfaces\ControllerInterface;
use Chevere\Utility\Str;

// IDEA Route lock (disables further modification)
// IDEA: Reg events, determine who changes a route.
// IDEA: Enable alt routes [/taken, /also-taken, /availabe]
// IDEA: L10n support

final class Route
{
    /** @const Array containing all the HTTP methods. */
    const HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'COPY', 'HEAD', 'OPTIONS', 'LINK', 'UNLINK', 'PURGE', 'LOCK', 'UNLOCK', 'PROPFIND', 'VIEW', 'TRACE', 'CONNECT'];

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
    public $id;

    /** @var string Route uri like /api/endpoint/{var?} */
    public $uri;

    /** @var string Route name (if any, must be unique) */
    public $name;

    /** @var array Where clauses based on wildcards */
    public $wheres;

    /** @var array ['methodName' => 'controller',] */
    public $methods;

    /** @var array ['middleware1', 'middleware2'] */
    public $middlewares;

    /** @var array */
    public $wildcards;

    /** @var string Key set representation */
    public $set;

    /** @var array An array containing all the key sets for the route (optionals combo) */
    public $powerSet;

    /** @var array An array containg details about the Route maker */
    public $maker;

    /** @var string */
    public $regex;

    /** @var string */
    public $type;

    /**
     * Route constructor.
     *
     * @param string $uri        Route uri (key string)
     * @param string $controller Callable for GET
     */
    public function __construct(string $uri, string $controller = null)
    {
        $this->uri = $uri;
        $keyValidation = new KeyValidation($this->uri);
        $this->maker = $this->getMakerData();
        if ($keyValidation->hasHandlebars) {
            $wildcards = new Wildcards($this->uri);
            $this->set = $wildcards->set;
            $this->powerSet = $wildcards->powerSet;
            $this->wildcards = $wildcards->wildcards;
        } else {
            $this->set = $this->uri;
        }
        $this->handleType();
        if (isset($controller)) {
            $this->setMethod('GET', $controller);
        }
    }

    /**
     * @param string $name route name, must be unique
     */
    public function setName(string $name): self
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

    /**
     * Sets where conditionals for the route wildcards.
     *
     * @param string $wildcardName wildcard name
     * @param string $regex        regex pattern
     */
    public function setWhere(string $wildcardName, string $regex): self
    {
        new WildcardValidation($wildcardName, $regex, $this);
        $this->wheres[$wildcardName] = $regex;

        return $this;
    }

    /**
     * Sets where conditionals for the route wildcards (multiple version).
     *
     * @param array $wildcardsPatterns An array containing [wildcardName => regexPattern,]
     */
    public function setWheres(array $wildcardsPatterns): self
    {
        foreach ($wildcardsPatterns as $wildcardName => $regexPattern) {
            $this->setWhere($wildcardName, $regexPattern);
        }

        return $this;
    }

    /**
     * Sets HTTP method to callable binding. Allocates Routes.
     *
     * @param string $httpMethod HTTP method
     * @param string $controller Controller which handles the request
     */
    public function setMethod(string $httpMethod, string $controller): self
    {
        // Validate HTTP method
        if (!in_array($httpMethod, static::HTTP_METHODS)) {
            throw new InvalidArgumentException(
                (new Message('Unknown HTTP method %s.'))
                    ->code('%s', $httpMethod)
                    ->toString()
            );
        }
        // FIXME: Unified validation (Controller validator)
        if (!is_subclass_of($controller, ControllerInterface::class)) {
            throw new LogicException(
                (new Message('Callable %s must represent a class implementing the %i interface.'))
                    ->code('%s', $controller)
                    ->code('%i', ControllerInterface::class)
                    ->toString()
            );
        }
        // Check HTTP dupes
        // if (isset($this->methods[$httpMethod])) {
        //     throw new InvalidArgumentException(
        //         (new Message('Method %s has been already registered.'))
        //             ->code('%s', $httpMethod)->toString()
        //     );
        // }
        $this->methods[$httpMethod] = $controller;

        return $this;
    }

    /**
     * Sets HTTP method to callable binding (multiple version).
     *
     * @param array $httpMethodsCallables An array containing [httpMethod => callable,]
     */
    public function setMethods(array $httpMethodsCallables): self
    {
        foreach ($httpMethodsCallables as $httpMethod => $controller) {
            $this->setMethod($httpMethod, $controller);
        }

        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function addMiddleware(string $callable): self
    {
        // $this->middlewares[] = $this->getCallableSome($callable);
        $this->middlewares[] = $callable;

        return $this;
    }

    /**
     * @param string $httpMethod an HTTP method
     */
    public function getController(string $httpMethod): string
    {
        $controller = $this->methods[$httpMethod] ?? null;
        if (!isset($controller)) {
            throw new LogicException(
                (new Message('No controller is associated to HTTP method %s.'))
                    ->code('%s', $httpMethod)
                    ->toString()
            );
        }

        return $controller;
    }

    /**
     * Fill object missing properties and whatnot.
     */
    public function fill(): self
    {
        if (isset($this->wildcards)) {
            foreach ($this->wildcards as $k => $v) {
                if (!isset($this->wheres[$v])) {
                    $this->wheres[$v] = static::REGEX_WILDCARD_WHERE;
                }
            }
        }
        if (isset($this->methods['GET']) && !isset($this->methods['HEAD'])) {
            $this->setMethod('HEAD', HeadController::class);
        }
        $this->regex = $this->regex();

        return $this;
    }

    /**
     * Gets route regex.
     *
     * @param string $set route set, null to use $this->set ?? $this->uri
     */
    public function regex(?string $set = null): string
    {
        $regex = $set ?? ($this->set ?? $this->uri);
        if (!isset($regex)) {
            throw new LogicException(
                (new Message('Unable to process regex for empty regex (no uri).'))->toString()
            );
        }
        $regex = '^'.$regex.'$';
        if (!Str::contains('{', $regex)) {
            return $regex;
        }
        if (isset($this->wildcards)) {
            foreach ($this->wildcards as $k => $v) {
                $regex = str_replace("{{$k}}", '('.$this->wheres[$v].')', $regex);
            }
        }

        return $regex;
    }

    /**
     * Binds a Route object.
     *
     * @param string $key      route key
     * @param string $callable Callable string
     */
    // public static function bind(string $key, string $callable = null, string $rootContext = null): self
    // {
    //     return new static(...func_get_args());
    // }

    private function getMakerData(): array
    {
        $maker = debug_backtrace(0, 3)[2];
        $maker['file'] = Path::relative($maker['file']);

        return $maker;
    }

    private function handleType()
    {
        if (!isset($this->set)) {
            $this->type = Route::TYPE_STATIC;
        } else {
            // Sets (optionals) are like /route/{0}
            $pregReplace = preg_replace('/{[0-9]+}/', '', $this->set);
            if (null != $pregReplace) {
                $pregReplace = trim(Path::normalize($pregReplace), '/');
            }
            $this->type = isset($pregReplace) ? Route::TYPE_DYNAMIC : Route::TYPE_STATIC;
        }
    }
}
