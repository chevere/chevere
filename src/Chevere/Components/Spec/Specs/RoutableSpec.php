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
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Router\Route\RouteWildcardInterface;
use Chevere\Interfaces\Spec\Specs\RoutableSpecInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecsInterface;
use function DeepCopy\deep_copy;

final class RoutableSpec implements RoutableSpecInterface
{
    use SpecsTrait;

    private RouteEndpointSpecsInterface $routeEndpointSpecs;

    private string $path;

    private string $regex;

    private array $wildcards;

    public function __construct(DirInterface $specDir, RoutableInterface $routable, string $repository)
    {
        $path = $routable->route()->path()->toString();
        $this->key = $repository . ':' . $path;
        $this->routeEndpointSpecs = new RouteEndpointSpecs();
        $specGroupRoute = $specDir
            ->getChild(ltrim($path, '/'));
        $this->jsonPath = $specGroupRoute->path()->toString() . 'route.json';
        $path = $routable->route()->path();
        $this->path = $path->toString();
        $this->regex = $path->regex()->toString();
        $this->wildcards = $path->wildcards()->toArray();
        $routeEndpoints = $routable->route()->endpoints();
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
        return deep_copy($this->routeEndpointSpecs);
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
            'spec' => $this->jsonPath,
            'path' => $this->path,
            'regex' => $this->regex,
            'wildcards' => $wildcards,
            'endpoints' => $endpoints,
        ];
    }
}
