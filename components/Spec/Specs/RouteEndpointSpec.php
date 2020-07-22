<?php

/*
 * This file is part of Chevereto.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\Spec\Specs\Traits\SpecsTrait;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Spec\SpecPathInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;

final class RouteEndpointSpec implements RouteEndpointSpecInterface
{
    use SpecsTrait;

    private array $array;

    public function __construct(SpecPathInterface $specPath, RouteEndpointInterface $routeEndpoint)
    {
        $this->key = $routeEndpoint->method()->name();
        $this->jsonPath = $specPath->getChild($this->key . '.json')->toString();
        $this->array = [
            'method' => $this->key,
            'spec' => $this->jsonPath,
            'description' => $routeEndpoint->description(),
            'parameters' => $routeEndpoint->parameters()
        ];
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
