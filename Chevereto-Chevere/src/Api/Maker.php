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
use Chevere\Str\Str;
use Chevere\Controller\Inspect;
use Chevere\Api\src\FilterIterator;
use Chevere\Cache\Cache;
use Chevere\Contracts\Api\MakerContract;
use Chevere\Controllers\Api\GetController;
use Chevere\Controllers\Api\HeadController;
use Chevere\Controllers\Api\OptionsController;
use Chevere\Router\Maker as RouterMaker;

final class Maker implements MakerContract
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

    public static function create(PathHandle $pathHandle, RouterMaker $routerMaker): MakerContract
    {
        $maker = new static($routerMaker);
        $methods = new Methods(
            (new Method('HEAD'))
                ->withController(HeadController::class),
            (new Method('OPTIONS'))
                ->withController(OptionsController::class),
            (new Method('GET'))
                ->withController(GetController::class)

        );
        $maker->register($pathHandle, new Endpoint($methods));

        return $maker;
    }

    public function api(): array
    {
        return $this->api;
    }

    public function withCache(): MakerContract
    {
        $new = clone $this;
        $new->cache = new Cache('api');
        $new->cache->put('api', $new->api)
            ->makeCache();

        return $new;
    }

    public function cache(): Cache
    {
        return $this->cache;
    }

    private function register(PathHandle $pathHandle, Endpoint $endpoint): void
    {
        $this->path = $pathHandle->path();
        $this->assertNoDuplicates();
        $this->assertPath();
        $this->basePath = strtolower(basename($this->path));
        $this->routesMap = [];
        $this->resourcesMap = [];
        $this->api = [];

        $iterator = new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = new FilterIterator($iterator);
        $filter = $filter->withAcceptFilenames(Method::ACCEPT_METHODS);
        $this->recursiveIterator = new RecursiveIteratorIterator($filter);
        $this->assertRecursiveIterator();
        $this->processRecursiveIterator();

        $this->processRoutesMap();

        $path = '/' . $this->basePath;
        $this->api[$this->basePath][''] = $endpoint->toArray();

        $route = new Route($path);
        $route
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
                (new Message('No API methods found in the %s path.'))
                    ->code('%s', $this->path)
                    ->toString()
            );
        }
    }

    private function assertNoDuplicates(): void
    {
        if (isset($this->registered[$this->path])) {
            throw new LogicException(
                (new Message('Path identified by %s has been already bound.'))
                    ->code('%s', $this->path)
                    ->toString()
            );
        }
    }

    private function assertPath(): void
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
                $methodsArray[] = (new Method($httpMethod))->withController($controller);
            }
            $methods = new Methods(...$methodsArray);
            $endpoint = new Endpoint($methods);
            /** @var string Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = Str::ltail($pathComponent, '/');

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
        $filepathRelative = Path::relative($filepath);
        $filepathNoExt = Str::replaceLast('.php', '', $filepathRelative);
        $filepathReplaceNS = Str::replaceFirst('app/src/', 'App\\', $filepathNoExt);

        return str_replace('/', '\\', $filepathReplaceNS);
    }
}
