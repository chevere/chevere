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

namespace Chevere\Api;

use OuterIterator;
use LogicException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use const Chevere\App\PATH as AppPath;
use Chevere\Route\Route;
use Chevere\Router\Router;
use Chevere\Api\src\FilterIterator;
use Chevere\Api\src\Endpoint;
use Chevere\Message;
use Chevere\Path;
use Chevere\File;
use Chevere\Utility\Str;
use Chevere\Controller\Inspect as ControllerInspect;
use Chevere\Controllers\Api\HeadController;
use Chevere\Controllers\Api\OptionsController;
use Chevere\Controllers\Api\GetController;

final class Maker
{
    /** @var array HTTP methods accepted by this filter [HTTP_METHOD,] */
    const ACCEPT_METHODS = Route::HTTP_METHODS;

    private $pathIdentifier;

    /** @var array Route mapping [route => [http_method => Controller]]] */
    private $routesMap;

    /** @var array Maps [endpoint => (array) resource [regex =>, description =>,]] (for wildcard routes) */
    private $resourcesMap;

    /** @var array Maps [Controller => ControllerInspect] */
    private $controllersMap;

    /** @var OuterIterator */
    private $recursiveIterator;

    /** @var array Endpoint API properties */
    private $api;

    /** @var string Target API directory (absolute) */
    private $directory;

    /** @var Router The injected Router, needed to add Routes to the injector instance */
    private $router;

    /** @var array Public exposed APIs groupped by basePath [basePath => [api],] */
    private $apis;

    /** @var string The API basepath, like 'api' */
    private $basePath;

    /** @var Route */
    private $route;

    /** @var string */
    private $uri;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function api(): array
    {
        return $this->api;
    }

    /**
     * Automatically finds controllers in the given path and generate the API route binding.
     *
     * @param string $pathIdentifier path identifier representing the dir containing API controllers (src/Api/)
     */
    public function register(string $pathIdentifier)
    {
        $this->pathIdentifier = Str::rtail($pathIdentifier, '/');
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
        $filter = (new FilterIterator($iterator))
            ->generateAcceptedFilenames(static::ACCEPT_METHODS, Api::METHOD_ROOT_PREFIX);

        $this->recursiveIterator = new RecursiveIteratorIterator($filter);

        $this->handleEmptyRecursiveIterator();
        $this->processRecursiveIteration();
        $this->processRoutesMap();

        $this->uri = '/'.$this->basePath;

        $httpMethods = [
            'HEAD' => HeadController::class,
            'OPTIONS' => OptionsController::class,
            'GET' => GetController::class,
        ];
        $endpoint = new Endpoint($httpMethods);
        $this->route = Route::bind($this->uri)
            ->setMethods($httpMethods)
            ->setId($this->basePath);
        $this->router->addRoute($this->route, $this->basePath);
        $this->api[$this->basePath][''] = $endpoint->toArray();
        ksort($this->api);
        $this->apis[$this->basePath] = true;
    }

    private function handleEmptyRecursiveIterator(): void
    {
        if (iterator_count($this->recursiveIterator) == 0) {
            throw new LogicException(
                (new Message('No API methods found in the %s path.'))
                    ->code('%s', $this->directory)
                    ->toString()
            );
        }
    }

    private function handleDuplicates(): void
    {
        if (isset($this->apis[$this->pathIdentifier])) {
            throw new LogicException(
                (new Message('Path identified by %s has been already bound.'))
                    ->code('%s', $this->pathIdentifier)
                    ->toString()
            );
        }
    }

    private function handleMissingDirectory(): void
    {
        if (!File::exists($this->directory)) {
            throw new LogicException(
                (new Message("Directory %s doesn't exists."))
                    ->code('%s', $this->directory)
                    ->toString()
            );
        }
    }

    private function processRecursiveIteration(): void
    {
        foreach ($this->recursiveIterator as $filename) {
            $filepathAbsolute = Str::forwardSlashes((string) $filename);
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

    private function processRoutesMap(): void
    {
        foreach ($this->routesMap as $pathComponent => $httpMethods) {
            $endpoint = new Endpoint($httpMethods);
            /** @var string Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = Str::ltail($pathComponent, '/');
            $this->route = Route::bind($endpointRouteKey)->setId($pathComponent)->setMethods($endpoint->getHttpMethods());
            // Define Route wildcard "where" if needed
            $resource = $this->resourcesMap[$pathComponent] ?? null;
            if (isset($resource)) {
                foreach ($resource as $wildcardKey => $resourceMeta) {
                    $this->route->setWhere($wildcardKey, $resourceMeta['regex']);
                }
                $endpoint->setResource($resource);
            }
            $this->router->addRoute($this->route, $this->basePath);
            $this->api[$this->basePath][$pathComponent] = $endpoint->toArray();
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
    private function getClassNameFromFilepath(string $filepath): string
    {
        $filepathRelative = Path::relative($filepath);
        $filepathNoExt = Str::replaceLast('.php', null, $filepathRelative);
        $filepathReplaceNS = Str::replaceFirst(AppPath.'src/', 'App\\', $filepathNoExt);

        return str_replace('/', '\\', $filepathReplaceNS);
    }
}
