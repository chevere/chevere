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

    /** @var string Route key like /api/endpoint/{var?} */
    protected $key;

    /** @var string Route name (if any, must be unique) */
    protected $name;

    /** @var array Where clauses based on wildcards */
    protected $wheres;

    /** @var array An array containg ['methodName' => 'callable',] */
    protected $methods;

    /** @var array An array containg Route middlewares */
    protected $middlewares;

    /** @var array An array containg wildcards */
    protected $wildcards;

    /** @var string Key set representation */
    protected $set;

    /** @var array An array containing all the key sets for the route (optionals combo) */
    protected $powerSet;

    /** @var array An array containg details about the Route maker */
    protected $maker;

    /** @var bool True if the route key has handlebars (wildcards) */
    protected $hasHandlebars;

    /** @var array An array containing the optional wildcards */
    protected $optionals;

    /** @var array An array indexing the optional wildcards */
    protected $optionalsIndex;

    /** @var array An array indexing the mandatory wildcards */
    protected $mandatoryIndex;

    /**
     * Route constructor.
     *
     * @param string $key      Route key string
     * @param string $callable Callable for GET
     */
    public function __construct(string $key, string $callable = null)
    {
        $this->key = $key;
        // Try, to catch the message 9hehe
        $routeKeyValidation = new RouteKeyValidation($this->key);
        $this->maker = $this->getMakerData();
        $this->processWildcards();
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
        $this->processWildcardValidation($wildcardName, $regex);
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
            $this->where($wildcardName, $regexPattern);
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

    public function getMethod(string $httpMethod): ?string
    {
        return $this->methods[$httpMethod] ?? null;
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

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function getMethods(): ?array
    {
        return $this->methods ?? null;
    }

    public function addMiddleware(string $callable): self
    {
        $this->middlewares[] = $this->getCallableSome($callable);

        return $this;
    }

    public function getMiddlewares(): ?array
    {
        return $this->middlewares ?? null;
    }

    public function getWildcards(): ?array
    {
        return $this->wildcards ?? null;
    }

    public function getSet(): ?string
    {
        return $this->set ?? null;
    }

    public function getPowerSet(): ?array
    {
        return $this->powerSet ?? null;
    }

    public function getMaker(): array
    {
        return $this->maker;
    }

    public static function getHandlebarsWrap(string $string): string
    {
        return "{{$string}}";
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
        if (isset($this->wildcards)) {
            foreach ($this->wildcards as $k => $v) {
                if (!isset($this->wheres[$v])) {
                    $this->wheres[$v] = static::REGEX_WILDCARD_WHERE;
                }
            }
        }

        return $this;
    }

    /**
     * Gets route regex depending on the passed key (if any).
     *
     * @param string $key route string to use, leave it blank to use $this->set ?? $this->key
     */
    public function regex(string $key = null): string
    {
        $regex = $key ?? $this->set ?? $this->key;
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

    protected function processWildcards(): void
    {
        if (preg_match_all(static::REGEX_WILDCARD_SEARCH, $this->key, $matches)) {
            // $matches[0] => [{wildcard}, {wildcard?},...]
            // $matches[1] => [wildcard, wildcard?,...]
            // Build the route handle, needed for regex replacements
            $this->set = $this->key;
            // Build the optionals array, needed for creating route power set if needed
            $this->optionals = [];
            $this->optionalsIndex = [];
            $this->processWildcardMatches($matches);
            $this->processOptionals();
        }
    }

    protected function processOptionals(): void
    {
        // Determine if route contains optional wildcards
        if (!empty($this->optionals)) {
            $mandatoryDiff = array_diff($this->wildcards ?? [], $this->optionalsIndex);
            $this->mandatoryIndex = [];
            foreach ($mandatoryDiff as $k => $v) {
                $this->mandatoryIndex[$k] = null;
            }
            // Generate the optionals power set, keeping its index keys in case of duplicated optionals
            $powerSet = Utils\Arr::powerSet($this->optionals, true);
            // Build the route set, it will contain all the possible route combinations
            $this->powerSet = $this->processPowerSet($powerSet);
        }
    }

    protected function processPowerSet(array $powerSet): array
    {
        $routeSet = [];
        foreach ($powerSet as $set) {
            $auxSet = $this->set;
            // auxWildcards keys represent the wildcards being used. Iterate it with foreach.
            $auxWildcards = $this->mandatoryIndex;
            foreach ($set as $replaceKey => $replaceValue) {
                $replace = $this->optionals[$replaceKey];
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

        return $routeSet;
    }

    protected function processWildcardMatches(array $matches): void
    {
        foreach ($matches[0] as $k => $v) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->set)) {
                $this->set = Utils\Str::replaceFirst($v, "{{$k}}", $this->set);
            }
            $wildcard = $matches[1][$k];
            if (Utils\Str::endsWith('?', $wildcard)) {
                $wildcardTrim = Utils\Str::replaceLast('?', null, $wildcard);
                $this->optionals[] = $k;
                $this->optionalsIndex[$k] = $wildcardTrim;
            } else {
                $wildcardTrim = $wildcard;
            }
            if (in_array($wildcardTrim, $this->wildcards ?? [])) {
                throw new CoreException(
                    (new Message('Must declare one unique wildcard per capturing group, duplicated %s detected in route %r.'))
                        ->code('%s', $matches[0][$k])
                        ->code('%r', $this->key)
                );
            }
            $this->wildcards[] = $wildcardTrim;
        }
    }

    protected function processWildcardValidation(string $wildcardName, string $regex): void
    {
        $wildcard = $this->getHandlebarsWrap($wildcardName);
        $validateFormat = !Utils\Str::startsWithNumeric($wildcardName) && preg_match('/^[a-z0-9_]+$/i', $wildcardName);
        if (!$validateFormat) {
            throw new CoreException(
                (new Message("String %s must contain only alphanumeric and underscore characters and it shouldn't start with a numeric value."))
                    ->code('%s', $wildcardName)
            );
        }
        $validateMatch = Utils\Str::contains($wildcard, $this->getKey()) || Utils\Str::contains('{'."$wildcardName?".'}', $this->getKey());
        if (!$validateMatch) {
            throw new CoreException(
                (new Message("Wildcard %s doesn't exists in %r."))
                    ->code('%s', $wildcard)
                    ->code('%r', $this->getKey())
            );
        }
        $validateUnique = !isset($this->wheres[$wildcardName]);
        if (!$validateUnique) {
            throw new CoreException(
                (new Message('Where clause for %s wildcard has been already declared.'))
                    ->code('%s', $wildcard)
            );
        }
        $validateRegex = Validate::regex('/'.$wildcardName.'/');
        if (!$validateRegex) {
            throw new CoreException(
                (new Message('Invalid regex pattern %s.'))
                    ->code('%s', $regex)
            );
        }
    }
}
