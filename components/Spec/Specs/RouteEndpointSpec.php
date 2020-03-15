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
    /** @var string /spec/group/route-name/GET.json */
    private string $specPath;

    private $array = [];

    public function __construct(
        RouteEndpointInterface $routeEndpoint,
        string $specPath
    ) {
        $this->specPath = $specPath;
        $this->array = [
            'method' => $routeEndpoint->method()->name(),
            'spec' => $specPath,
            'description' => $routeEndpoint->method()->description(),
            'parameters' => []
        ];
    }

    public function specPath(): string
    {
        return $this->specPath;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
