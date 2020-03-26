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

use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
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
        $this->wildcards = $routeable->route()->path()->wildcards()->toArray();
        $routeEndpointsMap = $routeable->route()->endpoints()->routeEndpointsMap();
        /** @var RouteEndpointInterface $routeEndpoint */
        foreach ($routeEndpointsMap->map() as $routeEndpoint) {
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

        return [
            'name' => $this->key,
            'spec' => $this->jsonPath,
            'path' => $this->path,
            'wildcards' => $this->wildcards,
            'endpoints' => $endpoints
        ];
    }
}
