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

use LogicException;
use ReflectionParameter;
use ReflectionMethod;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Api provides tools to create and retrieve the App Api.
 */
class Api
{
    /** @var string Prefix used for endpoints without a defined resource (/endpoint) */
    const METHOD_ROOT_PREFIX = '_';

    /** @var array HTTP methods accepted by this filter [HTTP_METHOD,] */
    const ACCEPT_METHODS = Route::HTTP_METHODS;

    /** @var Router The injected Router, needed to add Routes to the injector instance */
    protected $router;

    /** @var array [<endpoint> => [<options>]] */
    protected $api;

    /** @var array ['api-key' => [<options>]] */
    // protected $bases;

    /** @var array ['/api-key/v1/endpoint' => ['api-key', 'v1/endpoint']] */
    // protected $routeKeys;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Automatically finds controllers in the given path and generate the API route binding.
     *
     * @param string $pathIdentifier path identifier representing the dir containing API controllers (src/Api/)
     */
    public function register(string $pathIdentifier): self
    {
        $pathIdentifier = Utils\Str::rtail($pathIdentifier, '/');
        if (isset($this->api[$pathIdentifier])) {
            throw new LogicException(
                (string)
                    (new Message('Path identified by %s has been already bound.'))
                        ->code('%s', $pathIdentifier)
            );
        }
        /** @var string The API directory (absolute path) */
        $directoryAbsolute = Path::fromHandle($pathIdentifier);
        if (!File::exists($directoryAbsolute)) {
            throw new LogicException(
                (string)
                    (new Message("Directory %s doesn't exists."))
                        ->code('%s', $directoryAbsolute)
            );
        }
        /** @var string The API directory (relative path) */
        $directoryRelative = Path::relative($directoryAbsolute, App::APP);

        /** @var array Maps [route => [http_method => Controller]]] */
        $ROUTE_MAP = [];

        /** @var array Maps [Controller => ControllerInspect] */
        $CONTROLLERS = [];

        /** @var array Maps [endpoint => (array) resource [regex =>, description =>,]] (for wildcard routes) */
        $RESOURCED = [];

        /** @var array Public exposed API */
        $API = [];

        $errors = [];

        // Iterate the $directoryAbsolute filtering accepted filenames and folders
        $iterator = new RecursiveDirectoryIterator($directoryAbsolute, RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = (new ApiFilterIterator($iterator))
            ->generateAcceptedFilenames(static::ACCEPT_METHODS, static::METHOD_ROOT_PREFIX);
        $recursiveIterator = new RecursiveIteratorIterator($filter);

        $ee = [];
        foreach ($recursiveIterator as $filename) {
            $filepathAbsolute = Utils\Str::forwardSlashes((string) $filename);
            $ee[] = $filepathAbsolute;
            $className = $this->getClassNameFromFilepath($filepathAbsolute);
            $inspected = new ControllerInspect($className);
            $CONTROLLERS[$className] = $inspected;
            $pathComponent = $inspected->getPathComponent();
            if ($inspected->useResource()) {
                $RESOURCED[$pathComponent] = $inspected->getResourcesFromString();
                /*
                 * For relationships we need to create the /endpoint/{id}/relationships/relation URLs.
                 * @see https://jsonapi.org/recommendations/
                 */
                if ($inspected->isRelatedResource()) {
                    $ROUTE_MAP[$inspected->getRelationshipPathComponent()]['GET'] = $inspected->getRelationship();
                }
            }
            $ROUTE_MAP[$pathComponent][$inspected->getHttpMethod()] = $inspected->getClassName();
        }

        ksort($ROUTE_MAP);

        /** @var string The API basepath (usually 'api') */
        $basePath = explode('/', $pathComponent)[0];

        foreach ($ROUTE_MAP as $pathComponent => $httpMethods) {
            $api = [];
            // Set Options => <http method>,
            foreach ($httpMethods as $httpMethod => $controllerClassName) {
                $httpMethodOptions = [];
                $httpMethodOptions['description'] = $controllerClassName::getDescription();
                // if ($controllerClassName == 'App\Api\Users\Resource') {
                //     dd(\App\Api\Users\Resource::getDescription());
                // }
                $controllerParameters = $controllerClassName::getParameters();
                if (isset($controllerParameters)) {
                    $httpMethodOptions['parameters'] = $controllerParameters;
                }
                $api['OPTIONS'][$httpMethod] = $httpMethodOptions;
            }
            // Autofill OPTIONS and HEAD
            foreach ([
                'OPTIONS' => [
                    Controllers\ApiOptions::class, [
                        'description' => Controllers\ApiOptions::getDescription(),                    ],
                ],
                'HEAD' => [
                    Controllers\ApiHead::class, [
                        'description' => Controllers\ApiHead::getDescription(),
                    ],
                ],
            ] as $k => $v) {
                if (!isset($httpMethods[$k])) {
                    $httpMethods[$k] = $v[0];
                    $api['OPTIONS'][$k] = $v[1];
                }
            }
            /** @var string Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = Utils\Str::ltail($pathComponent, '/');
            $route = Route::bind($endpointRouteKey)->setId($pathComponent)->setMethods($httpMethods);
            // Define Route wildcard "where" if needed
            $resource = $RESOURCED[$pathComponent] ?? null;
            if (isset($resource)) {
                foreach ($resource as $wildcardKey => $resourceMeta) {
                    $route->setWhere($wildcardKey, $resourceMeta['regex']);
                }
                $api['resource'] = $resource;
            }
            $API[$pathComponent] = $api;
            $this->getRouter()->addRoute($route, $basePath);
            $API[$pathComponent] = $api;
        }
        ksort($API);

        $route = Route::bind('/'.$basePath)
            ->setMethod('HEAD', Controllers\ApiHead::class)
            ->setMethod('OPTIONS', Controllers\ApiOptions::class)
            ->setMethod('GET', Controllers\ApiGet::class)
            ->setId($basePath);
        $this->getRouter()->addRoute($route, $basePath);
        $this->api[$basePath] = $API;
        $baseOpts = [
            'HEAD' => Controllers\ApiHead::OPTIONS,
            'OPTIONS' => Controllers\ApiOptions::OPTIONS,
            'GET' => Controllers\ApiGet::OPTIONS,
        ];
        $this->bases[$basePath] = ['OPTIONS' => $baseOpts];
        $this->routeKeys['/'.$basePath] = [$basePath];

        return $this;
    }

    /**
     * Returns the namespaced class name for the specified filepath.
     *
     * @param string $filepath the class filepath
     *
     * @return string the class name detected according autoloading standard (PSR-4)
     */
    protected function getClassNameFromFilepath(string $filepath): string
    {
        $filepathRelative = Path::relative($filepath);
        $filepathNoExt = Utils\Str::replaceLast('.php', null, $filepathRelative);
        $filepathReplaceNS = Utils\Str::replaceFirst(App\PATH.'src/', APP_NS_HANDLE, $filepathNoExt);

        return str_replace('/', '\\', $filepathReplaceNS);
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    // public function getKeys(): array
    // {
    //     return array_keys($this->api);
    // }

    // public function getBaseOptions(string $key): ?array
    // {
    //     return $this->bases[ltrim($key, '/')] ?? null;
    // }

    public function getEndpoint(string $key): ?array
    {
        $keys = $this->routeKeys[$key] ?? null;
        if (isset($keys)) {
            $api = $this->get($keys[0]);
            if (isset($keys[1])) {
                return $api[$keys[1]];
            } else {
                return $api ?? null;
            }
        } else {
            return null;
        }
    }

    /**
     * Gets the API key of the alleged endpoint key.
     */
    public function getEndpointApiKey(string $key): ?string
    {
        $keys = $this->routeKeys[$key] ?? null;
        if (isset($keys)) {
            return $keys[0];
        }

        return null;
    }

    /**
     * Get a exposed API array.
     *
     * @param string $key the API key (base pathName)
     */
    public function get(string $key = 'api'): ?array
    {
        return $this->api[$key] ?? null;
    }

    public function getRouteKeys(): ?array
    {
        return $this->routeKeys ?? null;
    }

    /**
     * Get the error associated with invalid controller __invoke(Class $hint).
     */
    protected static function getInvokeHintError(string $filename, string $class = null, ReflectionMethod $invoke, ReflectionParameter $param): ?string
    {
        if (null === $class || !class_exists($class)) {
            $error = 'Class <code>%c</code> doesn\'t exist or it hasn\'t being loaded, the system is unable to resolve implicit <code>%v</code> binding in <code>%f:%l:%n</code>';
        } elseif (!method_exists($class, '__construct')) {
            $error = 'Unable to typehint object <code>%c</code> (no constructor defined)';
        }

        return isset($error) ? strtr($error, [
            '%n' => ($param->getPosition() + 1),
            '%l' => $invoke->getStartLine(),
            '%v' => '$'.$param->name,
            '%c' => $class,
            '%f' => $filename,
        ]) : null;
    }
}
class ApiException extends CoreException
{
}
