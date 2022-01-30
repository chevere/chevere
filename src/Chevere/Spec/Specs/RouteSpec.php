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

namespace Chevere\Spec\Specs;

use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Router\Interfaces\Route\RouteInterface;
use Chevere\Router\Interfaces\Route\RouteWildcardInterface;
use Chevere\Spec\Interfaces\Specs\RouteEndpointSpecsInterface;
use Chevere\Spec\Interfaces\Specs\RouteSpecInterface;
use Chevere\Spec\Specs\Traits\SpecsTrait;
use function Chevere\VarSupport\deepCopy;

final class RouteSpec implements RouteSpecInterface
{
    use SpecsTrait;

    private string $locator;

    private RouteEndpointSpecsInterface $routeEndpointSpecs;

    private string $path;

    private string $regex;

    private array $wildcards;

    public function __construct(DirInterface $specDir, RouteInterface $route, string $repository)
    {
        $path = $route->path();
        $this->path = $path->name();
        $this->key = $path->name();
        $this->locator = $repository . ':' . $this->key;
        $this->routeEndpointSpecs = new RouteEndpointSpecs();
        $specGroupRoute = $specDir
            ->getChild(ltrim($this->path, '/') . '/');
        $this->jsonPath = $specGroupRoute->path()->__toString() . 'route.json';

        $this->regex = $path->regex()->__toString();
        $this->wildcards = $path->wildcards()->toArray();
        $routeEndpoints = $route->endpoints();
        /** @var string $key */
        foreach ($routeEndpoints->keys() as $key) {
            $routeEndpointSpec = new RouteEndpointSpec(
                $specGroupRoute,
                $routeEndpoints->get($key)
            );
            $this->routeEndpointSpecs = $this->routeEndpointSpecs
                ->withPut($routeEndpointSpec);
        }
    }

    public function clonedRouteEndpointSpecs(): RouteEndpointSpecs
    {
        return deepCopy($this->routeEndpointSpecs);
    }

    public function toArray(): array
    {
        $endpoints = [];
        foreach ($this->routeEndpointSpecs->getIterator() as $key => $routeEndpointSpec) {
            $endpoints[$key] = $routeEndpointSpec->toArray();
        }
        $wildcardsArray = [];
        /** @var RouteWildcardInterface $wildcard */
        foreach ($this->wildcards as $wildcard) {
            $wildcardsArray[$wildcard->__toString()] = '^' . $wildcard->match()->__toString() . '$';
        }

        return [
            'name' => $this->key,
            'locator' => $this->locator,
            'spec' => $this->jsonPath,
            'regex' => $this->regex,
            'wildcards' => $wildcardsArray,
            'endpoints' => $endpoints,
        ];
    }
}
