<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Api;

use Chevere\Components\Api\src\FilterIterator;
use Chevere\Components\Cache\CacheKey;
use Chevere\Components\Cache\Traits\CacheAccessTrait;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Controller\Inspect;
use Chevere\Components\Controllers\Api\GetController;
use Chevere\Components\Controllers\Api\HeadController;
use Chevere\Components\Controllers\Api\OptionsController;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\MethodControllerName;
use Chevere\Components\Http\MethodControllerNameCollection;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Api\Interfaces\ApiMakerInterface;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\HeadMethod;
use Chevere\Components\Http\Methods\OptionsMethod;
use Chevere\Components\Str\Str;
use Chevere\Components\Route\Interfaces\RouteInterface;
use LogicException;
use OuterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Throwable;

final class ApiMaker implements ApiMakerInterface
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

    /** @var RouteInterface */
    private $route;

    /** @var PathInterface For target API directory */
    private $path;

    /** @var CacheInterface */
    private $cache;

    public function __construct(RouterMaker $routerMaker)
    {
        $this->routerMaker = $routerMaker;
    }

    public function withPath(PathInterface $path): ApiMakerInterface
    {
        $new = clone $this;
        $new->path = $path;
        $new->assertNoDuplicates();
        $new->assertPath();
        $new->basePath = strtolower(basename($new->path->absolute()));
        $methodControllerCollection = new MethodControllerNameCollection(
            new MethodControllerName(
                new HeadMethod(),
                new ControllerName(HeadController::class)
            ),
            new MethodControllerName(
                new OptionsMethod(),
                new ControllerName(OptionsController::class)
            ),
            new MethodControllerName(
                new GetMethod(),
                new ControllerName(GetController::class)
            )
        );
        $new->register(new Endpoint($methodControllerCollection));

        return $new;
    }

    public function withCache(CacheInterface $cache): ApiMakerInterface
    {
        $new = clone $this;
        $new->cache = $cache
            ->withPut(
                new CacheKey(CacheKeys::API),
                new VariableExport($new->api)
            );

        return $new;
    }

    public function hasApi(): bool
    {
        return isset($this->api);
    }

    public function hasPath(): bool
    {
        return isset($this->path);
    }

    public function api(): array
    {
        return $this->api;
    }

    public function path(): PathInterface
    {
        return $this->path;
    }

    private function register(Endpoint $endpoint): void
    {
        $this->routesMap = [];
        $this->resourcesMap = [];
        $this->api = [];

        $iterator = new RecursiveDirectoryIterator($this->path->absolute(), RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = new FilterIterator($iterator);
        $filter = $filter->withAcceptFilenames(MethodInterface::ACCEPT_METHOD_NAMES);
        $this->recursiveIterator = new RecursiveIteratorIterator($filter);
        $this->assertRecursiveIterator();
        $this->processRecursiveIterator();

        $this->processRoutesMap();

        $path = '/' . $this->basePath;
        $this->api[$this->basePath][''] = $endpoint->toArray();

        $route = new Route(new PathUri($path));
        foreach ($endpoint->methodControllerNameCollection()->toArray() as $method) {
            $route = $route->withAddedMethod($method->method(), $method->controllerName());
        }

        $this->routerMaker = $this->routerMaker
            ->withAddedRouteable(
                new Routeable($route),
                $this->basePath
            );

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
        if (0 == $count) {
            throw new LogicException(
                (new Message('No API methods found in the %path% path'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    private function assertNoDuplicates(): void
    {
        if (isset($this->registered[$this->path->absolute()])) {
            throw new LogicException(
                (new Message('Path identified by %path% has been already bound'))
                    ->code('%path%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    private function assertPath(): void
    {
        if (!$this->path->exists()) {
            throw new LogicException(
                (new Message("Directory %directory% doesn't exists"))
                    ->code('%directory%', $this->path->absolute())
                    ->toString()
            );
        }
        if (!is_readable($this->path->absolute())) {
            throw new LogicException(
                (new Message('Directory %directory% is not readable'))
                    ->code('%directory%', $this->path->absolute())
                    ->toString()
            );
        }
    }

    private function processRecursiveIterator(): void
    {
        foreach ($this->recursiveIterator as $filename) {
            $filepathAbsolute = (string) (new Str((string) $filename))->forwardSlashes();
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
            /** Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = (string) (new Str($pathComponent))->leftTail('/');
            $this->route = new Route(new PathUri($endpointRouteKey));
            foreach ($httpMethods as $httpMethod => $controller) {
                $this->route = $this->route
                    ->withAddedMethod(
                        new Method($httpMethod),
                        new ControllerName($controller)
                    );
            }
            $endpoint = new Endpoint($this->route->methodControllerNameCollection());
            $this->routerMaker = $this->routerMaker
                ->withAddedRouteable(
                    new Routeable($this->route),
                    $this->basePath
                );
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
        $filepathRelative = (new AppPath($filepath))->relative();
        $filepathNoExt = (new Str($filepathRelative))->replaceLast('.php', '');
        $filepathReplaceNS = (string) (new Str($filepathNoExt))->replaceFirst('app/src/', 'App\\');

        return str_replace('/', '\\', $filepathReplaceNS);
    }
}
