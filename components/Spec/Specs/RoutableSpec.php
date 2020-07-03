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

use Chevere\Components\Spec\Specs\RouteEndpointSpecs;
use Chevere\Components\Spec\Specs\Traits\SpecsTrait;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteWildcardInterface;
use Chevere\Interfaces\Router\RoutableInterface;
use Chevere\Interfaces\Spec\SpecInterface;
use Chevere\Interfaces\Spec\SpecPathInterface;
use function DeepCopy\deep_copy;

final class RoutableSpec implements SpecInterface
{
    use SpecsTrait;

    private RouteEndpointSpecs $routeEndpointSpecs;

    private string $path;

    private string $regex;

    private array $wildcards;

    /**
     * @var SpecPathInterface $specGroupPath /spec/group
     */
    public function __construct(
        SpecPathInterface $specGroupPath,
        RoutableInterface $routable
    ) {
        $this->key = $routable->route()->name()->toString();
        $this->routeEndpointSpecs = new RouteEndpointSpecs;
        $specGroupRoute = $specGroupPath->getChild($this->key);
        $this->jsonPath = $specGroupRoute->getChild('route.json')->pub();
        $this->path = $routable->route()->path()->toString();
        $this->regex = $routable->route()->path()->regex()->toNoDelimiters();
        $this->wildcards = $routable->route()->path()->wildcards()->mapCopy()->toArray();
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

    public function routeEndpointSpecs(): RouteEndpointSpecs
    {
        return deep_copy($this->routeEndpointSpecs);
    }

    public function toArray(): array
    {
        $endpoints = [];
        /**
         * @var string $key
         * @var RouteEndpointSpec $routeEndpointSpec
         */
        foreach ($this->routeEndpointSpecs->map() as $key => $routeEndpointSpec) {
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
            'endpoints' => $endpoints
        ];
    }
}
