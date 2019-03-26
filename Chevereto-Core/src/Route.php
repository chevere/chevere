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

// TODO: Route lock (disables further modification)
// TODO: Reg events, determine who changes a route.
// TODO: Enable alt routes [/taken, /also-taken, /availabe]
// TODO: L10n support
// FIXME: Each new Route() only creates the object (checks object integrity) and do not collect.

/**
 * This one works with Routes static.
 */
class Route
{
    use Traits\CallableTrait;
    /**
     * Array containing all the HTTP methods.
     */
    const HTTP_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'COPY', 'HEAD', 'OPTIONS', 'LINK', 'UNLINK', 'PURGE', 'LOCK', 'UNLOCK', 'PROPFIND', 'VIEW', 'TRACE',  'CONNECT'];

    /**
     * Route without wildcards.
     */
    const TYPE_STATIC = 'static';
    /**
     * Route containing wildcards and static components.
     */
    const TYPE_MIXED = 'mixed';
    /**
     * Route containing only wildcards, no static components.
     */
    const TYPE_DYNAMIC = 'dynamic';
    /**
     * Regex pattern used by default (no explicit where).
     */
    const REGEX_WILDCARD_WHERE = '[A-z0-9_\-\%]+';
    /**
     * Regex pattern used to detect {wildcard} and {wildcard?}.
     */
    const REGEX_WILDCARD_SEARCH = '/{([a-z_][\w_]*\??)}/i';
    /**
     * Regex pattern used to validate route name.
     */
    const REGEX_NAME = '/^[\w\-\.]+$/i';

    protected $id;
    protected $key;
    protected $name;
    protected $methods = [];
    protected $wildcards = [];
    protected $maker = [];
    protected $wheres;
    protected $set;
    protected $powerSet;
    protected $middlewares;

    /**
     * Route constructor.
     *
     * @param string $key      route string
     * @param string $callable callable for GET
     */
    public function __construct(string $key, string $callable = null)
    {
        $hasHandlebars = Utils\Str::contains('{', $key) || Utils\Str::contains('}', $key);
        try {
            Validation::grouped('$key', $key)
                ->append(
                    'value',
                    function (string $string): bool {
                        return
                            $string == '/' ?: (
                                strlen($string) > 0
                                && Utils\Str::startsWith('/', $string)
                                && Utils\Str::endsWith('/', $string) == false
                                && Utils\Str::contains('//', $string) == false
                                && Utils\Str::contains(' ', $string) == false
                                && Utils\Str::contains('\\', $string) == false
                            );
                    },
                    "String %i must start with a forward slash, it shouldn't contain neither whitespace, backslashes or extra forward slashes and it should be specified without a trailing slash."
                )
                // ->append(
                //     'unique',
                //     function (string $string): bool {
                //         $collection = Routes::instance();
                //         if ($collection == null) {
                //             return true;
                //         }

                //         return isset($collection->getUniques()[$string]) == false;
                //     },
                //     'Route %s has been already declared.'
                // )
                ->append(
                    'wildcards',
                    function (string $string) use ($hasHandlebars): bool {
                        return !$hasHandlebars ?: preg_match_all('/{([0-9]+)}/', $string) == false;
                    },
                    (string) (new Message('Wildcards in the form of %s are reserved.'))
                        ->code('%s', '{n}')
                )
                ->validate();
        } catch (Exception $e) {
            throw new RouteException($e);
        }
        $maker = debug_backtrace(0, 1)[0];
        $maker['file'] = Path::relative($maker['file']);
        $this->maker = $maker;
        $this->key = $key;

        // Detect valid wildcards
        if ($hasHandlebars && preg_match_all(static::REGEX_WILDCARD_SEARCH, $key, $wildcards)) {
            // $wildcards[0] => [{wildcard}, {wildcard?},...]
            // $wildcards[1] => [wildcard, wildcard?,...]
            // Build the route handle, needed for regex replacements
            $this->set = $key;
            // Build the optionals array, needed for creating route power set if needed
            $optionals = [];
            $optionalsIndex = [];
            foreach ($wildcards[0] as $k => $v) {
                // Change {wildcard} to {n} (n is the wildcard index)
                if ($this->set != null) {
                    $this->set = Utils\Str::replaceFirst($v, "{{$k}}", $this->set);
                }
                $wildcard = $wildcards[1][$k];
                if (Utils\Str::endsWith('?', $wildcard)) {
                    $wildcardTrim = Utils\Str::replaceLast('?', null, $wildcard);
                    $optionals[] = $k;
                    $optionalsIndex[$k] = $wildcardTrim;
                } else {
                    $wildcardTrim = $wildcard;
                }
                if (in_array($wildcardTrim, $this->wildcards)) {
                    throw new RouteException(
                        (new Message('Must declare one unique wildcard per capturing group, duplicated %s detected in route %r.'))
                            ->code('%s', $wildcards[0][$k])
                            ->code('%r', $key)
                    );
                }
                $this->wildcards[] = $wildcardTrim;
            }
            // Determine if route contains optional wildcards
            if ($optionals) {
                $mandatoryDiff = array_diff($this->wildcards, $optionalsIndex);
                $mandatorIndex = [];
                foreach ($mandatoryDiff as $k => $wildcard) {
                    $mandatorIndex[$k] = null;
                }
                // Generate the optionals power set, keeping its index keys in case of duplicated optionals
                $powerSet = Utils\Arr::powerSet($optionals, true);
                // Build the route set, it will contain all the possible route combinations
                $routeSet = [];
                foreach ($powerSet as $set) {
                    $auxSet = $this->set;
                    // auxWildcards keys represent the wildcards being used. Iterate it with foreach.
                    $auxWildcards = $mandatorIndex;
                    foreach ($set as $replaceKey => $replaceValue) {
                        $replace = $optionals[$replaceKey];
                        if ($replaceValue !== null) {
                            $replaceValue = "{{$replaceValue}}";
                            $auxWildcards[$replace] = null;
                        }
                        $auxSet = str_replace("{{$replace}}", $replaceValue ?? '', $auxSet);
                        $auxSet = Path::normalize($auxSet);
                    }
                    ksort($auxWildcards);
                    /*
                     * Maps expected regex indexed matches [0,1,2,] to registered wildcard index [index=>n].
                     * For example, a set /test-{0}--{2} will capture 0->0 and 1->2. Storing the expected index allows\
                     * to easily map matches => wildcards => values.
                     */
                    $routeSet[$auxSet] = array_keys($auxWildcards);
                }
                $this->powerSet = $routeSet;
            }
        }
        if ($callable) {
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
                // ->append(
                //     'unique',
                //     function (string $string): bool {
                //         return isset(Routes::instance()->getNamed()[$string]) == false;
                //     },
                //     'Route name %s has been already taken.'
                // )
                ->validate();
        } catch (Exception $e) {
            throw new RouteException($e);
        }
        $this->name = $name;
        // $this->collect();

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
        if (in_array($httpMethod, static::HTTP_METHODS) == false) {
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
        // $this->collect();

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
        // The actual {wildcard}
        $wildcard = "{{$wildcardName}}";
        // Validate $wildcardName
        try {
            Validation::grouped('$wildcardName', $wildcardName)
                ->append(
                    'value',
                    function (string $string): bool {
                        return
                            Utils\Str::startsWithNumeric($string) == false
                            && preg_match('/^[a-z0-9_]+$/i', $string);
                    },
                    "String %s must contain only alphanumeric and underscore characters and it shouldn't start with a numeric value."
                )
                ->append(
                    'match',
                    function (string $string) use ($wildcard): bool {
                        return
                            Utils\Str::contains($wildcard, $this->key)
                            || Utils\Str::contains('{'."$string?".'}', $this->key);
                    },
                    (string) (new Message("Wildcard %s doesn't exists in %r."))
                        ->code('%s', $wildcard)
                        ->code('%r', $this->key)
                )
                ->append(
                    'unique',
                    function (string $string): bool {
                        return isset($this->wheres[$string]) == false;
                    },
                    (string) (new Message('Where clause for %s wildcard has been already declared.'))
                        ->code('%s', $wildcard)
                )
                ->validate();
            Validation::single(
                '$regex',
                $regex,
                function (string $string): bool {
                    return Validate::regex('/'.$string.'/');
                },
                'Invalid regex pattern %s.'
            );
        } catch (Exception $e) {
            throw new RouteException($e->getMessage());
        }
        $this->wheres[$wildcardName] = $regex;
        // $this->collect();

        return $this;
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
     * Get a defined callable (callable string, callable absolute).
     *
     * @param string $httpMethod an HTTP method
     */
    public function getCallable(string $httpMethod): string
    {
        $callable = $this->methods[$httpMethod] ?? null;
        if ($callable == null) {
            throw new RouteException(
                (new Message('No controller is associated to HTTP method %s.'))
                    ->code('%s', $httpMethod)
            );
        }
        // if (Utils\Str::endsWith('.php', $callable)) {
        //     $callable = Path::absolute($callable);
        // }
        return $callable;
    }

    /**
     * Collects the Route object in the Routes instance.
     */
    // protected function collect(): self
    // {
    //     $collection = Routes::instance() ?? new Routes();
    //     $collection->allocate($this);

    //     return $this;
    // }

    /**
     * Fill object missing properties and whatnot.
     */
    public function fill(): self
    {
        foreach ($this->wildcards as $k => $v) {
            if (isset($this->wheres[$v]) == false) {
                $this->wheres[$v] = static::REGEX_WILDCARD_WHERE;
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
        $regex = $key ?? $this->set ?? $this->key;
        if ($regex == null) {
            throw new RouteException(
                (new Message('Unable to process regex for empty %s.'))
                    ->code('%s', '$key')
            );
        }
        $regex = '^'.$regex.'$';
        if (Utils\Str::contains('{', $regex) == false) {
            return $regex;
        }
        foreach ($this->wildcards as $k => $v) {
            $regex = str_replace("{{$k}}", '('.$this->wheres[$v].')', $regex);
        }

        return $regex;
    }

    public function middleware(string $callable): self
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

    public function getPowerSet(): ?array
    {
        return $this->powerSet;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSet(): ?string
    {
        return $this->set;
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get a route object from the Routes. Public alias of Routes::getRoute.
     *
     * @param mixed $idOrName route id (int); Route name (string)
     */
    // public static function get($idOrName): self
    // {
    //     $object = Routes::instance()->getRoute(...func_get_args());

    //     return $object;
    // }

    /**
     * Binds a Route object.
     *
     * @param string $key      route key
     * @param string $callable get Callable
     */
    public static function bind(string $key, string $callable = null, string $rootContext = null): self
    {
        return new static(...func_get_args());
    }

    /**
     * Unbind a Route (/route) from Routes.
     *
     * @param string $key Route string (as defined using ::bind())
     *
     * @return bool TRUE if the route was removed
     *
     * @throws RoutesException if the route doesn't exists
     */
    public static function unbind(string $key): bool
    {
        return Routes::instance()->removeRoute($key, true);
    }

    /*
     * Removes a Route from Routes.
     *
     * @param mixed $idOrName route id (int); Route name (string)
     *
     * @throws RoutesException if the route doesn't exists
     */
    // public static function remove($idOrName): bool
    // {
    //     return Routes::instance()->removeRoute($idOrName);
    // }
}

class RouteException extends CoreException
{
}
