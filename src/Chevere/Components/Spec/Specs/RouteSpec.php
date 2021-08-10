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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\Spec\Specs\Traits\SpecsTrait;
use function Chevere\Components\Var\deepCopy;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\Route\RouteInterface;
use Chevere\Interfaces\Router\Route\RouteWildcardInterface;
use Chevere\Interfaces\Spec\Specs\RouteSpecInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecsInterface;

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
        $this->jsonPath = $specGroupRoute->path()->toString() . 'route.json';

        $this->regex = $path->regex()->toString();
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
        foreach ($this->routeEndpointSpecs->getGenerator() as $key => $routeEndpointSpec) {
            $endpoints[$key] = $routeEndpointSpec->toArray();
        }
        $wildcards = [];
        /** @var RouteWildcardInterface $wildcard */
        foreach ($this->wildcards as $wildcard) {
            $wildcards[$wildcard->toString()] = '^' . $wildcard->match()->toString() . '$';
        }

        return [
            'name' => $this->key,
            'locator' => $this->locator,
            'spec' => $this->jsonPath,
            'regex' => $this->regex,
            'wildcards' => $wildcards,
            'endpoints' => $endpoints,
        ];
    }
}
