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
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\Specs\RouteEndpointSpecObjectsRead;
use SplObjectStorage;

final class RouteableSpec implements ToArrayInterface
{
    private SplObjectStorage $objects;

    private string $jsonPath;

    private $array = [];

    /**
     * @var SpecPathInterface $specPath /spec/group/route-name
     */
    public function __construct(
        SpecPathInterface $specPath,
        RouteableInterface $routeable
    ) {
        $this->objects = new SplObjectStorage;
        $this->jsonPath = $specPath->getChild('route.json')->pub();
        $this->array = [
            'name' => $routeable->route()->name()->toString(),
            'spec' => $this->jsonPath,
            'path' => $routeable->route()->path()->toString(),
            'wildcards' => $routeable->route()->path()->routeWildcards()->toArray(),
        ];
        $objects = $routeable->route()->endpoints()->objects();
        $objects->rewind();
        while ($objects->valid()) {
            $routeEndpointSpec = new RouteEndpointSpec(
                $specPath,
                (new RouteEndpoint(
                    $objects->current()->method(),
                    $objects->current()->controller()
                ))
                    ->withDescription($objects->current()->description())
                    ->withParameters($objects->current()->parameters())
            );
            $this->objects->attach($routeEndpointSpec);
            $this->array['endpoints'][] = $routeEndpointSpec->toArray();
            $objects->next();
        }
    }

    public function jsonPath(): string
    {
        return $this->jsonPath;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function objects(): RouteEndpointSpecObjectsRead
    {
        return new RouteEndpointSpecObjectsRead($this->objects);
    }
}
