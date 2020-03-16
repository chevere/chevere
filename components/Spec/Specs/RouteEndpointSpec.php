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
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;

final class RouteEndpointSpec implements ToArrayInterface
{
    /** @var string /spec/group/route-name/ */
    private string $jsonPath;

    private $array = [];

    public function __construct(
        string $specPath,
        RouteEndpointInterface $routeEndpoint
    ) {
        $this->jsonPath = $specPath . $routeEndpoint->method()->name() . '.json';
        $this->array = [
            'method' => $routeEndpoint->method()->name(),
            'spec' => $this->jsonPath,
            'description' => $routeEndpoint->description(),
            'parameters' => $routeEndpoint->parameters()
        ];
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
