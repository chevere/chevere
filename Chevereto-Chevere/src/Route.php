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

namespace Chevereto\Chevere;

use Exception;
use LogicException;

// IDEA Route lock (disables further modification)
// IDEA: Reg events, determine who changes a route.
// IDEA: Enable alt routes [/taken, /also-taken, /availabe]
// IDEA: L10n support

class Route implements Interfaces\RouteInterface
{
    use Traits\CallableTrait;

    /** @const Array containing all the HTTP methods. */
    const HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'COPY', 'HEAD', 'OPTIONS', 'LINK', 'UNLINK', 'PURGE', 'LOCK', 'UNLOCK', 'PROPFIND', 'VIEW', 'TRACE', 'CONNECT'];

    /** @const string Route without wildcards. */
    const TYPE_STATIC = 'static';

    /** @const string Route containing wildcards and static components. */
    const TYPE_MIXED = 'mixed';

    /** @const string Route containing only wildcards, no static components. */
    const TYPE_DYNAMIC = 'dynamic';

    /** @const string Regex pattern used by default (no explicit where). */
    const REGEX_WILDCARD_WHERE = '[A-z0-9\_\-\%]+';

    /** @const string Regex pattern used to detect {wildcard} and {wildcard?}. */
    const REGEX_WILDCARD_SEARCH = '/{([a-z\_][\w_]*\??)}/i';

    /** @const string Regex pattern used to validate route name. */
    const REGEX_NAME = '/^[\w\-\.]+$/i';

    /** @var string Route id relative to the ArrayFile */
    protected $id;

    /** @var string Route uri like /api/endpoint/{var?} */
    protected $uri;

    /** @var string Route name (if any, must be unique) */
    protected $name;

    /** @var array Where clauses based on wildcards */
    protected $wheres;

    /** @var array An array containg ['methodName' => 'callable',] */
    protected $methods;

    /** @var array An array containg Route middlewares */
    protected $middlewares;

    /** @var array */
    protected $wildcards;

    /** @var string Key set representation */
    protected $set;

    /** @var array An array containing all the key sets for the route (optionals combo) */
    protected $powerSet;

    /** @var array An array containg details about the Route maker */
    protected $maker;

    /**
     * Route constructor.
     *
     * @param string $uri      Route uri (key string)
     * @param string $callable Callable for GET
     */
    public function __construct(string $uri, string $callable = null)
    {
        $this->uri = $uri;
        // TODO: Try, to catch the message 9hehe
        $keyValidation = new RouteKeyValidation($this->uri);
        $this->maker = $this->getMakerData();
        if ($keyValidation->hasHandlebars) {
            $routeWildcards = new RouteWildcards($this->uri);
            $this->set = $routeWildcards->set;
            $this->powerSet = $routeWildcards->powerSet;
            $this->wildcards = $routeWildcards->wildcards;
        }
        if (isset($callable)) {
            $this->setMethod('GET', $callable);
        }
    }

    /**
     * @param string $name route name, must be unique
     */
    public function setName(string $name): self
    {
        // Validate $name
        if (!preg_match(Route::REGEX_NAME, $name)) {
            throw new Exception(
                (string)
                    (new Message("Expecting at least one alphanumeric, underscore, hypen or dot character. String '%s' provided."))
                        ->code('%s', $name)
                        ->code('%p', Route::REGEX_NAME)
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
        new RouteWildcardValidation($wildcardName, $regex, $this);
        $this->wheres[$wildcardName] = $regex;

        return $this;
    }

    public function getWhere(string $wildcardName): ?string
    {
        return $this->wheres[$wildcardName] ?? null;
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

    public function getWheres(): ?array
    {
        return $this->wheres ?? null;
    }

    /**
     * Sets HTTP method to callable binding. Allocates Routes.
     *
     * @param string $httpMethod HTTP method
     * @param string $callable   callable which satisfy the method request
     */
    public function setMethod(string $httpMethod, string $callable): self
    {
        // Validate HTTP method
        if (!in_array($httpMethod, static::HTTP_METHODS)) {
            throw new CoreException(
                (new Message('Unknown HTTP method %s.'))->code('%s', $httpMethod)
            );
        }
        $callableSome = $this->getCallableSome($callable);
        // Check HTTP dupes
        // if (isset($this->methods[$httpMethod])) {
        //     throw new CoreException(
        //         (new Message('Method %s has been already registered.'))
        //             ->code('%s', $httpMethod)
        //     );
        // }
        $this->methods[$httpMethod] = $callableSome;

        return $this;
    }

    public function getMethodCallable(string $httpMethod): ?string
    {
        return $this->methods[$httpMethod];
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMethods(): ?array
    {
        return $this->methods;
    }

    public function addMiddleware(string $callable): self
    {
        $this->middlewares[] = $this->getCallableSome($callable);

        return $this;
    }

    public function getMiddlewares(): ?array
    {
        return $this->middlewares;
    }

    public function getWildcards(): ?array
    {
        return $this->wildcards;
    }

    public function getSet(): ?string
    {
        return $this->set;
    }

    public function getPowerSet(): ?array
    {
        return $this->powerSet;
    }

    public function getMaker(): array
    {
        return $this->maker;
    }

    /**
     * Get a defined route callable.
     *
     * @param string $httpMethod an HTTP method
     */
    public function getCallable(string $httpMethod): string
    {
        // TODO: NO HEAD CALLABLE (auto for GET)
        $callable = $this->methods[$httpMethod] ?? null;
        if (!isset($callable)) {
            throw new LogicException(
                (string)
                    (new Message('No callable is associated to HTTP method %s.'))
                        ->code('%s', $httpMethod)
            );
        }

        return $callable;
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
            $this->setMethod('HEAD', Controllers\Head::class);
        }

        return $this;
    }

    /**
     * Gets route regex depending on the passed key (if any).
     *
     * @param string $key route string to use, leave it blank to use $this->set ?? $this->uri
     */
    public function regex(string $key = null): string
    {
        $regex = $key ?? $this->set ?? $this->uri;
        if (!isset($regex)) {
            throw new CoreException(
                (new Message('Unable to process regex for empty %s.'))
                    ->code('%s', '$key')
            );
        }
        $regex = '^'.$regex.'$';
        if (!Utils\Str::contains('{', $regex)) {
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
    public static function bind(string $key, string $callable = null, string $rootContext = null): self
    {
        return new static(...func_get_args());
    }

    protected function getMakerData(): array
    {
        $maker = debug_backtrace(0, 1)[0];
        $maker['file'] = Path::relative($maker['file']);

        return $maker;
    }
}
