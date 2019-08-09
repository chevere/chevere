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
use const Chevere\App\PATH as AppPath;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\HttpFoundation\Method;
use Chevere\HttpFoundation\Methods;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Api\src\FilterIterator;
use Chevere\Api\src\Endpoint;
use Chevere\Message;
use Chevere\Path;
use Chevere\PathHandle;
use Chevere\File;
use Chevere\Utility\Str;
use Chevere\Controller\Inspect as ControllerInspect;
use Chevere\Controllers\Api\HeadController;
use Chevere\Controllers\Api\OptionsController;
use Chevere\Controllers\Api\GetController;
use Chevere\Contracts\Api\MakerContract;

final class Maker implements MakerContract
{
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

    /** @var RouterContract The injected Router, needed to add Routes to the injector instance */
    private $router;

    /** @var array Contains registered API paths via register() */
    private $registered;

    /** @var string The API basepath, like 'api' */
    private $basePath;

    /** @var RouteContract */
    private $route;

    /** @var string Target API directory (absolute) */
    private $path;

    public function __construct(RouterContract $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function register(PathHandle $pathHandle): void
    {
        $this->path = $pathHandle->path();
        $this->validateNoDuplicates();
        $this->validatePath();
        $this->basePath = strtolower(basename($this->path));
        $this->routesMap = [];
        $this->resourcesMap = [];
        $this->controllersMap = [];
        $this->api = [];

        $iterator = new RecursiveDirectoryIterator($this->path, RecursiveDirectoryIterator::SKIP_DOTS);
        $filter = new FilterIterator($iterator);
        $filter->generateAcceptedFilenames(Method::ACCEPT_METHODS, Api::METHOD_ROOT_PREFIX);
        $this->recursiveIterator = new RecursiveIteratorIterator($filter);
        $this->validateRecursiveIterator();
        $this->processRecursiveIterator();

        $this->processRoutesMap();

        $path = '/'.$this->basePath;

        $methods = new Methods();
        $methods->add(new Method('HEAD', HeadController::class));
        $methods->add(new Method('OPTIONS', OptionsController::class));
        $methods->add(new Method('GET', GetController::class));

        $endpoint = new Endpoint($methods);

        $this->route = (new Route($path))
            ->setMethods($methods)
            ->setId($this->basePath);

        $this->router->addRoute($this->route, $this->basePath);
        $this->api[$this->basePath][''] = $endpoint->toArray();
        ksort($this->api);
        $this->registered[$this->basePath] = true;
    }

    public function api(): array
    {
        return $this->api;
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
            $methods = new Methods();
            foreach ($httpMethods as $httpMethod => $controller) {
                $methods->add(new Method($httpMethod, $controller));
            }
            $endpoint = new Endpoint($methods);
            /** @var string Full qualified route key for $pathComponent like /api/users/{user} */
            $endpointRouteKey = Str::ltail($pathComponent, '/');
            $this->route = (new Route($endpointRouteKey))
                ->setId($pathComponent)
                ->setMethods($endpoint->methods());
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
