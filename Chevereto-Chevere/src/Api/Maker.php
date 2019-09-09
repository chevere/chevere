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
use Throwable;
use const Chevere\APP_PATH_RELATIVE;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Http\Method;
use Chevere\Http\Methods;
use Chevere\Message;
use Chevere\Path\Path;
use Chevere\Path\PathHandle;
use Chevere\File;
use Chevere\Utility\Str;
use Chevere\Controller\Inspect;
use Chevere\Api\src\FilterIterator;
use Chevere\Cache\Cache;
use Chevere\Controllers\Api\GetController;
use Chevere\Controllers\Api\HeadController;
use Chevere\Controllers\Api\OptionsController;
use Chevere\Router\Maker as RouterMaker;

final class Maker
{
    /** @var array Route mapping [route => [http_method => Controller]]] */
    private $routesMap;

    /** @var array Maps [endpoint => (array) resource [regex =>, description =>,]] (for wildcard routes) */
    private $resourcesMap;

    /** @var OuterIterator */
    private $recursiveIterator;

    /** @var array Endpoint API properties */
    private $api;

    /** @var RouterMaker The injected Router, needed to add Routes to the injector instance */
    private $routerMaker;

    /** @var array Contains registered API paths via register() */
    private $registered;

    /** @var string The API basepath, like 'api' */
    private $basePath;

    /** @var RouteContract */
    private $route;

    /** @var string Target API directory (absolute) */
    private $path;

    /** @var Cache */
    private $cache;

    public function __construct(RouterMaker $router)
    {
        $this->routerMaker = $router;
    }

    public static function create(PathHandle $pathHandle, RouterMaker $routerMaker)
    {
        $maker = new static($routerMaker);
        $methods = new Methods(
            new Method('HEAD', HeadController::class),
            new Method('OPTIONS', OptionsController::class),
            new Method('GET', GetController::class)
        );
        $maker->register($pathHandle, new Endpoint($methods));
        return $maker;
    }

    public function register(PathHandle $pathHandle, Endpoint $endpoint): void
    {
        $this->path = $pathHandle->path();
        $this->validateNoDuplicates();
        $this->validatePath();
        $this->basePath = strtolower(basename($this->path));
        $this->routesMap = [];
        $this->resourcesMap = [];
        // $this->controllersMap = [];
        $this->api = [];

        $iterator = new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = new FilterIterator($iterator);
        $filter->generateAcceptedFilenames(Method::ACCEPT_METHODS, Api::METHOD_ROOT_PREFIX);
        $this->recursiveIterator = new RecursiveIteratorIterator($filter);
        $this->validateRecursiveIterator();
        $this->processRecursiveIterator();

        $this->processRoutesMap();

        $path = '/' . $this->basePath;
        $this->api[$this->basePath][''] = $endpoint->toArray();

        $route = new Route($path);
        $route->setMethods($endpoint->methods())->setId($this->basePath);
        $this->routerMaker->addRoute($route, $this->basePath);

        $this->registered[$this->basePath] = true;
        ksort($this->api);
    }

    public function api(): array
    {
        return $this->api;
    }

    public function setCache(): void
    {
        $this->cache = new Cache('api');
        $this->cache->put('api', $this->api);
    }

    public function cache(): Cache
    {
        return $this->cache;
    }

    private function validateRecursiveIterator(): void
    {
        try {
            $count = iterator_count($this->recursiveIterator);
        } catch (Throwable $e) {
            throw new LogicException($e->getMessage());
        }
        if ($count == 0) {
            throw new LogicException(
                (new Message('No API methods found in the %s path.'))
                    ->code('%s', $this->path)
                    ->toString()
            );
        }
    }

    private function validateNoDuplicates(): void
    {
        if (isset($this->registered[$this->path])) {
            throw new LogicException(
                (new Message('Path identified by %s has been already bound.'))
                    ->code('%s', $this->path)
                    ->toString()
            );
        }
    }

    private function validatePath(): void
    {
        if (!File::exists($this->path)) {
            throw new LogicException(
                (new Message("Directory %s doesn't exists."))
                    ->code('%s', $this->path)
                    ->toString()
            );
        }
        if (!is_readable($this->path)) {
            throw new LogicException(
                (new Message('Directory %s is not readable.'))
                    ->code('%s', $this->path)
                    ->toString()
            );
        }
    }

    private function processRecursiveIterator(): void
    {
        foreach ($this->recursiveIterator as $filename) {
            $filepathAbsolute = Str::forwardSlashes((string) $filename);
            $className = $this->getClassNameFromFilepath($filepathAbsolute);
            $inspected = new Inspect($className);
            // $this->controllersMap[$className] = $inspected;
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
            $methodsArray = [];
            foreach ($httpMethods as $httpMethod => $controller) {
                $methodsArray[] = new Method($httpMethod, $controller);
            }
            $methods = new Methods(...$methodsArray);
            $endpoint = new Endpoint($methods);
            /** @var string Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = Str::ltail($pathComponent, '/');

            $this->route = (new Route($endpointRouteKey))
                ->setId($pathComponent)
                ->setMethods($methods);
            $resource = $this->resourcesMap[$pathComponent] ?? null;
            if (isset($resource)) {
                foreach ($resource as $wildcardKey => $resourceMeta) {
                    $this->route->setWhere($wildcardKey, $resourceMeta['regex']);
                }
                $endpoint->setResource($resource);
            }
            $this->routerMaker->addRoute($this->route, $this->basePath);
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
        $filepathNoExt = Str::replaceLast('.php', '', $filepathRelative);
        $filepathReplaceNS = Str::replaceFirst(APP_PATH_RELATIVE . 'src/', 'App\\', $filepathNoExt);

        return str_replace('/', '\\', $filepathReplaceNS);
    }
}
