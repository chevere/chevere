<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Exception;
use Symfony\Component\Console\Exception\LogicException;

// IDEA Route lock (disables further modification)
// IDEA: Reg events, determine who changes a route.
// IDEA: Enable alt routes [/taken, /also-taken, /availabe]
// IDEA: L10n support

/**
 * ALLFATHER GIVE ME ROUTEH!
 *
 * @method string hasId(): bool
 * @method string hasKey(): bool
 * @method string hasName(): bool
 * @method string hasWheres(): bool
 * @method string hasSet(): bool
 * @method string hasPowerSet(): bool
 * @method string hasMethods(): bool
 * @method string hasWildcards(): bool
 * @method string hasMaker(): bool
 * @method string hasMiddlewares(): bool
 * @method string hasHandlebars(): bool
 * @method string hasOptionals(): bool
 * @method string hasOptionalsIndex(): bool
 * @method string hasMandatoryIndex(): bool
 */
class Route extends RouteProcesssor
{
    use Traits\ContainerTrait;
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

    /** @var string */
    protected $id;
    protected $key;
    protected $name;
    protected $wheres;
    protected $set;
    protected $powerSet;

    /** @var array */
    protected $methods;
    protected $wildcards;
    protected $maker;
    protected $middlewares;

    /** @var bool */
    protected $handlebars;

    /** @var array */
    protected $optionals;

    /** @var array */
    protected $optionalsIndex;

    /** @var array */
    protected $mandatoryIndex;

    /**
     * Route constructor.
     *
     * @param string $key      Route key string
     * @param string $callable Callable for GET
     */
    public function __construct(string $key, string $callable = null)
    {
        $this->handlebars = Utils\Str::contains('{', $key) || Utils\Str::contains('}', $key);
        $this->processKeyValidation($key);
        $this->setKey($key);
        $this->processMaker();
        $this->processWildcards();

        if (isset($callable)) {
            $this->method('GET', $callable);
        }
    }

    /**
     * Set route name.
     *
     * @param string $name
     */
    public function name(string $name): self
    {
        // Validate $name
        try {
            Validation::grouped('$name', $name)
                ->append(
                    'value',
                    function (string $string): bool {
                        return (bool) preg_match(Route::REGEX_NAME, $string);
                    },
                    (string) (new Message("Expecting at least one alphanumeric, underscore, hypen or dot character. String '%s' provided."))
                        ->code('%p', Route::REGEX_NAME)
                )
                ->validate();
        } catch (Exception $e) {
            throw new RouteException($e);
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Sets HTTP method to callable binding. Allocates Routes.
     *
     * @param string $httpMethod HTTP method
     * @param string $callable   callable which satisfy the method request
     */
    public function method(string $httpMethod, string $callable): self
    {
        // Validate HTTP method
        if (!in_array($httpMethod, static::HTTP_METHODS)) {
            throw new RouteException(
                (new Message('Unknown HTTP method %s.'))->code('%s', $httpMethod)
            );
        }
        $callableSome = $this->getCallableSome($callable);
        // Check HTTP dupes
        // if (isset($this->methods[$httpMethod])) {
        //     throw new RouteException(
        //         (new Message('Method %s has been already registered.'))
        //             ->code('%s', $httpMethod)
        //     );
        // }
        $this->methods[$httpMethod] = $callableSome;

        return $this;
    }

    /**
     * Sets HTTP method to callable binding (multiple version).
     *
     * @param array $httpMethodsCallables An array containing [httpMethod => callable,]
     */
    public function methods(array $httpMethodsCallables): self
    {
        foreach ($httpMethodsCallables as $httpMethod => $controller) {
            $this->method($httpMethod, $controller);
        }

        return $this;
    }

    /**
     * Sets where conditionals for the route wildcards.
     *
     * @param string $wildcardName wildcard name
     * @param string $regex        regex pattern
     */
    public function where(string $wildcardName, string $regex): self
    {
        // Validate $wildcardName
        $this->processWildcardValidation($wildcardName, $regex);
        $this->wheres[$wildcardName] = $regex;

        return $this;
    }

    public static function getHandlebarsWrap(string $string): string
    {
        return "{{$string}}";
    }

    /**
     * Sets where conditionals for the route wildcards (multiple version).
     *
     * @param array $wildcardsPatterns An array containing [wildcardName => regexPattern,]
     */
    public function wheres(array $wildcardsPatterns): self
    {
        foreach ($wildcardsPatterns as $wildcardName => $regexPattern) {
            $this->where($wildcardName, $regexPattern);
        }

        return $this;
    }

    /**
     * Get a defined route callable.
     *
     * @param string $httpMethod an HTTP method
     */
    public function getCallable(string $httpMethod): string
    {
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
        if ($this->hasWildcards()) {
            foreach ($this->getWildcards() as $k => $v) {
                if (!isset($this->wheres[$v])) {
                    $this->wheres[$v] = static::REGEX_WILDCARD_WHERE;
                }
            }
        }

        return $this;
    }

    /**
     * Get route regex.
     *
     * @param string $key route string to use, leave it blank to use $this->set ?? $this->key
     */
    public function regex(string $key = null): string
    {
        $regex = $key ?? $this->set ?? $this->getKey();
        if (!isset($regex)) {
            throw new RouteException(
                (new Message('Unable to process regex for empty %s.'))
                    ->code('%s', '$key')
            );
        }
        $regex = '^'.$regex.'$';
        if (!Utils\Str::contains('{', $regex)) {
            return $regex;
        }
        foreach ($this->getWildcards() as $k => $v) {
            $regex = str_replace("{{$k}}", '('.$this->wheres[$v].')', $regex);
        }

        return $regex;
    }

    public function middleware(string $callable): self
    {
        $this->middlewares[] = $this->getCallableSome($callable);

        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getWildcards(): array
    {
        return $this->wildcards;
    }

    public function getPowerSet(): array
    {
        return $this->powerSet;
    }

    protected function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSet(): string
    {
        return $this->set;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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
}

class RouteException extends CoreException
{
}
