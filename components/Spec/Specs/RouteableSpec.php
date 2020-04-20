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

namespace Chevere\Components\Spec;

use Chevere\Components\Regex\Interfaces\RegexInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardInterface;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Spec\Interfaces\SpecInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\Specs\RouteEndpointSpecs;
use Chevere\Components\Spec\Specs\Traits\SpecsTrait;
use function DeepCopy\deep_copy;

final class RouteableSpec implements SpecInterface
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
        RouteableInterface $routeable
    ) {
        $this->key = $routeable->route()->name()->toString();
        $this->routeEndpointSpecs = new RouteEndpointSpecs;
        $specGroupRoute = $specGroupPath->getChild($this->key);
        $this->jsonPath = $specGroupRoute->getChild('route.json')->pub();
        $this->path = $routeable->route()->path()->toString();
        $this->regex = $routeable->route()->path()->regex()->toNoDelimiters();
        $this->wildcards = $routeable->route()->path()->wildcards()->toArray();
        $routeEndpoints = $routeable->route()->endpoints();
        /** @var RouteEndpointInterface $routeEndpoint */
        foreach ($routeEndpoints->map() as $routeEndpoint) {
            $routeEndpointSpec = new RouteEndpointSpec(
                $specGroupRoute,
                $routeEndpoint
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
