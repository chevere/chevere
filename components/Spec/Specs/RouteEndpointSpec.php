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

use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Spec\Interfaces\SpecInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\Specs\Traits\SpecsTrait;

final class RouteEndpointSpec implements SpecInterface
{
    use SpecsTrait;

    /**
     * @var SpecPathInterface $specRoutePath /spec/group/route
     */
    public function __construct(
        SpecPathInterface $specRoutePath,
        RouteEndpointInterface $routeEndpoint
    ) {
        $this->key = $routeEndpoint->method()->name();
        $this->jsonPath = $specRoutePath->getChild($this->key . '.json')->pub();
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
