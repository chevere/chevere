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

use OuterIterator;
use LogicException;
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

    protected $pathIdentifier;

    /** @var array Route mapping [route => [http_method => Controller]]] */
    protected $routesMap;

    /** @var array Maps [endpoint => (array) resource [regex =>, description =>,]] (for wildcard routes) */
    protected $resourcesMap;

    /** @var array Maps [Controller => ControllerInspect] */
    protected $controllersMap;

    /** @var OuterIterator */
    protected $recursiveIterator;

    /** @var array Endpoint API properties */
    protected $api;

    /** @var string Target API directory (absolute) */
    protected $directory;

    /** @var Router The injected Router, needed to add Routes to the injector instance */
    protected $router;

    /** @var array Public exposed APIs groupped by basePath [basePath => [api],] */
    protected $apis;

    /** @var array Contains ['/api/route/algo' => [id, 'route/algo']] */
    protected $routeUris;

    /** @var string The API basepath, like 'api' */
    private $basePath;

    /** @var Route */
    private $route;

    /** @var string */
    private $uri;

    /** @var Router */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Automatically finds controllers in the given path and generate the API route binding.
     *
     * @param string $pathIdentifier path identifier representing the dir containing API controllers (src/Api/)
     */
    public function register(string $pathIdentifier)
    {
        $this->pathIdentifier = Utility\Str::rtail($pathIdentifier, '/');
        $this->handleDuplicates();
        $this->directory = Path::fromHandle($this->pathIdentifier);
        $this->handleMissingDirectory();
        $this->basePath = strtolower(basename($this->directory));
        $this->routesMap = [];
        $this->resourcesMap = [];
        $this->controllersMap = [];
        $this->api = [];

        // Iterate the $this->directory filtering accepted filenames and folders
        $iterator = new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = (new ApiFilterIterator($iterator))
            ->generateAcceptedFilenames(static::ACCEPT_METHODS, static::METHOD_ROOT_PREFIX);

        $this->recursiveIterator = new RecursiveIteratorIterator($filter);
        $this->handleEmptyRecursiveIterator();
        $this->processRecursiveIteration();

        $this->processRoutesMap();

        $this->uri = '/'.$this->basePath;

        $httpMethods = [
            'HEAD' => Controllers\Api\Head::class,
            'OPTIONS' => Controllers\Api\Options::class,
            'GET' => Controllers\Api\Get::class,
        ];
        $apiEndpoint = new ApiEndpoint($httpMethods);

        $this->route = Route::bind($this->uri)
            ->setMethods($httpMethods)
            ->setId($this->basePath);
        $this->getRouter()->addRoute($this->route, $this->basePath);
        $this->apis[$this->basePath] = $apiEndpoint->toArray();
    }

    protected function handleEmptyRecursiveIterator(): void
    {
        if (iterator_count($this->recursiveIterator) == 0) {
            throw new LogicException(
                (string)
                    (new Message('No API methods found in the %s path.'))
                        ->code('%s', $this->directory)
            );
        }
    }

    protected function handleDuplicates(): void
    {
        if (isset($this->apis[$this->pathIdentifier])) {
            throw new LogicException(
                (string)
                    (new Message('Path identified by %s has been already bound.'))
                        ->code('%s', $this->pathIdentifier)
            );
        }
    }

    protected function handleMissingDirectory(): void
    {
        if (!File::exists($this->directory)) {
            throw new LogicException(
                (string)
                    (new Message("Directory %s doesn't exists."))
                        ->code('%s', $this->directory)
            );
        }
    }

    protected function processRecursiveIteration(): void
    {
        foreach ($this->recursiveIterator as $filename) {
            $filepathAbsolute = Utility\Str::forwardSlashes((string) $filename);
            $className = $this->getClassNameFromFilepath($filepathAbsolute);
            $inspected = new ControllerInspect($className);
            $this->controllersMap[$className] = $inspected;
            $pathComponent = $inspected->pathComponent;
            if ($inspected->useResource) {
                $this->resourcesMap[$pathComponent] = $inspected->resourcesFromString;
                /*
                 * For relationships we need to create the /endpoint/{id}/relationships/relation URLs.
                 * @see https://jsonapi.org/recommendations/
                 */
                if ($inspected->isRelatedResource) {
                    $this->routesMap[$inspected->relationshipPathComponent]['GET'] = $inspected->relationship;
                }
            }
            $this->routesMap[$pathComponent][$inspected->httpMethod] = $inspected->className;
        }
        ksort($this->routesMap);
    }

    protected function processRoutesMap(): void
    {
        foreach ($this->routesMap as $pathComponent => $httpMethods) {
            $apiEndpoint = new ApiEndpoint($httpMethods);
            /** @var string Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = Utility\Str::ltail($pathComponent, '/');
            $this->route = Route::bind($endpointRouteKey)->setId($pathComponent)->setMethods($apiEndpoint->getHttpMethods());
            // Define Route wildcard "where" if needed
            $resource = $this->resourcesMap[$pathComponent] ?? null;
            if (isset($resource)) {
                foreach ($resource as $wildcardKey => $resourceMeta) {
                    $this->route->setWhere($wildcardKey, $resourceMeta['regex']);
                }
                $apiEndpoint->setResource($resource);
            }
            $this->getRouter()->addRoute($this->route, $this->basePath);
            $this->api[$pathComponent] = $apiEndpoint->toArray();
        }
        ksort($this->api);
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
        $filepathNoExt = Utility\Str::replaceLast('.php', null, $filepathRelative);
        $filepathReplaceNS = Utility\Str::replaceFirst(App\PATH.'src/', APP_NS_HANDLE, $filepathNoExt);

        return str_replace('/', '\\', $filepathReplaceNS);
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getEndpoint(string $endpoint): ?array
    {
        $routeKey = $this->getUriApiKey($endpoint);
        if ($routeKey) {
            $api = $this->get($routeKey);
            if (isset($api[$endpoint])) {
                return $api[$endpoint];
            }

            return $api;
        }

        return null;
    }

    /**
     * Gets the API key of the alleged endpoint key.
     */
    public function getUriApiKey(string $uri): string
    {
        $endpoint = ltrim($uri, '/');
        $base = strtok($endpoint, '/');

        if (!isset($this->apis[$base])) {
            throw new LogicException(
                (string) (new Message('No API for the %s URI.'))
                    ->code('%s', $uri)
            );
        }

        return $base;
    }

    /**
     * Get a exposed API array.
     *
     * @param string $key the API key (base pathName)
     */
    public function get(string $key = 'api'): ?array
    {
        return $this->apis[$key] ?? null;
    }
}
