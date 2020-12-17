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
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Spec\Specs\RouteEndpointSpecInterface;

final class RouteEndpointSpec implements RouteEndpointSpecInterface
{
    use SpecsTrait;

    private array $array;

    public function __construct(DirInterface $specDir, RouteEndpointInterface $routeEndpoint)
    {
        $this->key = $routeEndpoint->method()->name();
        $this->jsonPath = $specDir
            ->getChild($this->key . '/')
            ->path()
            ->toString() . '.json';
        $this->array = [
            'method' => $this->key,
            'spec' => $this->jsonPath,
            'description' => $routeEndpoint->description(),
            'parameters' => $routeEndpoint->parameters(),
        ];
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
