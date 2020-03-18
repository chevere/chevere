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

use Chevere\Components\Common\Interfaces\ToArrayInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;

final class RouteEndpointSpec implements ToArrayInterface
{
    private MethodInterface $method;

    private string $jsonPath;

    private $array = [];

    public function __construct(
        SpecPathInterface $specPath,
        RouteEndpointInterface $routeEndpoint
    ) {
        $this->method = $routeEndpoint->method();
        $this->jsonPath = $specPath->getChild($this->method::name() . '.json')->pub();
        $this->array = [
            'method' => $this->method::name(),
            'spec' => $this->jsonPath,
            'description' => $routeEndpoint->description(),
            'parameters' => $routeEndpoint->parameters()
        ];
    }

    public function method(): MethodInterface
    {
        return $this->method;
    }

    public function jsonPath(): string
    {
        return $this->jsonPath;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
