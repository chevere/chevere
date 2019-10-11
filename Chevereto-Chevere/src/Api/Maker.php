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
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Http\Method;
use Chevere\Http\Methods;
use Chevere\Message\Message;
use Chevere\Path\Path;
use Chevere\Path\PathHandle;
use Chevere\File\File;
use Chevere\Controller\Inspect;
use Chevere\Api\src\FilterIterator;
use Chevere\Cache\Cache;
use Chevere\Cache\Traits\CacheAccessTrait;
use Chevere\Contracts\Api\MakerContract;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Controllers\Api\GetController;
use Chevere\Controllers\Api\HeadController;
use Chevere\Controllers\Api\OptionsController;
use Chevere\Router\Maker as RouterMaker;

use function ChevereFn\stringForwardSlashes;
use function ChevereFn\stringLeftTail;
use function ChevereFn\stringReplaceFirst;
use function ChevereFn\stringReplaceLast;

final class Maker implements MakerContract
{
    use CacheAccessTrait;

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

    /** @var PathHandle For target API directory (absolute) */
    private $pathHandle;

    /** @var Cache */
    private $cache;

    public function __construct(RouterMaker $routerMaker)
    {
        $this->routerMaker = $routerMaker;
    }

    public function withPathHandle(PathHandle $pathHandle): MakerContract
    {
        $new = clone $this;
        $new->pathHandle = $pathHandle;
        $new->assertNoDuplicates();
        $new->assertPath();
        $new->basePath = strtolower(basename($new->pathHandle->path()));
        $methods = new Methods(
            (new Method('HEAD'))
                ->withController(HeadController::class),
            (new Method('OPTIONS'))
                ->withController(OptionsController::class),
            (new Method('GET'))
                ->withController(GetController::class)

        );
        $new->register(new Endpoint($methods));

        return $new;
    }

    public function withCache(): MakerContract
    {
        $new = clone $this;
        $new->cache = new Cache('api');
        $new->cache->put('api', $new->api)
            ->makeCache();

        return $new;
    }

    public function hasApi(): bool
    {
        return isset($this->api);
    }

    public function hasPathHandle(): bool
    {
        return isset($this->pathHandle);
    }

    public function api(): array
    {
        return $this->api;
    }

    public function pathHandle(): PathHandle
    {
        return $this->pathHandle;
    }

    private function register(Endpoint $endpoint): void
    {
        $this->routesMap = [];
        $this->resourcesMap = [];
        $this->api = [];

        $iterator = new RecursiveDirectoryIterator($this->pathHandle->path(), RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = new FilterIterator($iterator);
        $filter = $filter->withAcceptFilenames(MethodContract::ACCEPT_METHODS);
        $this->recursiveIterator = new RecursiveIteratorIterator($filter);
        $this->assertRecursiveIterator();
        $this->processRecursiveIterator();

        $this->processRoutesMap();

        $path = '/' . $this->basePath;
        $this->api[$this->basePath][''] = $endpoint->toArray();

        $route = new Route($path);
        $route = $route
            ->withMethods($endpoint->methods())
            ->withId($this->basePath);

        $this->routerMaker = $this->routerMaker
            ->withAddedRoute($route, $this->basePath);

        $this->registered[$this->basePath] = true;
        ksort($this->api);
    }

    private function assertRecursiveIterator(): void
    {
        try {
            $count = iterator_count($this->recursiveIterator);
        } catch (Throwable $e) {
            throw new LogicException($e->getMessage());
        }
        if ($count == 0) {
            throw new LogicException(
                (new Message('No API methods found in the %path% path'))
                    ->code('%path%', $this->pathHandle->path())
                    ->toString()
            );
        }
    }

    private function assertNoDuplicates(): void
    {
        if (isset($this->registered[$this->pathHandle->path()])) {
            throw new LogicException(
                (new Message('Path identified by %path% has been already bound'))
                    ->code('%path%', $this->pathHandle->path())
                    ->toString()
            );
        }
    }

    private function assertPath(): void
    {
        $file = $this->pathHandle->file();
        if (!$file->exists()) {
            throw new LogicException(
                (new Message("Directory %directory% doesn't exists"))
                    ->code('%directory%', $this->pathHandle->path())
                    ->toString()
            );
        }
        if (!is_readable($this->pathHandle->path())) {
            throw new LogicException(
                (new Message('Directory %directory% is not readable.'))
                    ->code('%directory%', $this->pathHandle->path())
                    ->toString()
            );
        }
    }

    private function processRecursiveIterator(): void
    {
        foreach ($this->recursiveIterator as $filename) {
            $filepathAbsolute = stringForwardSlashes((string) $filename);
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
                $methodsArray[] = (new Method($httpMethod))->withController($controller);
            }
            $methods = new Methods(...$methodsArray);
            $endpoint = new Endpoint($methods);
            /** @var string Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = stringLeftTail($pathComponent, '/');

            $this->route = (new Route($endpointRouteKey))
                ->withId($pathComponent)
                ->withMethods($methods);

            // $resource = $this->resourcesMap[$pathComponent] ?? null;
            // if (isset($resource)) {
            //     foreach ($resource as $wildcardKey => $resourceMeta) {
            //         $this->route->setWhere($wildcardKey, $resourceMeta['regex']);
            //     }
            //     $endpoint->setResource($resource);
            // }

            $this->routerMaker = $this->routerMaker
                ->withAddedRoute($this->route, $this->basePath);

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
        $filepathRelative = (new Path($filepath))->relative();
        $filepathNoExt = stringReplaceLast('.php', '', $filepathRelative);
        $filepathReplaceNS = stringReplaceFirst('app/src/', 'App\\', $filepathNoExt);

        return str_replace('/', '\\', $filepathReplaceNS);
    }
}
