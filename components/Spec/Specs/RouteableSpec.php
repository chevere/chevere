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

final class RouteableSpec implements ToArrayInterface
{
    private $array = [];

    // $filePath = '/spec/group/route-name/route.json'
    public function __construct(RouteableInterface $routeable, string $filePath)
    {
        $this->array = [
            'name' => $routeable->route()->name()->toString(),
            'spec' => $filePath,
            'path' => $routeable->route()->path()->toString(),
            'wildcards' => $routeable->route()->path()->routeWildcards()->toArray(),
        ];
        $objects = $routeable->route()->methodControllers()->objects();
        $objects->rewind();
        $endpoints = [];
        while ($objects->valid()) {
            $routeEndpoint = new RouteEndpoint(
                $objects->current()->method(),
                $objects->current()->controller()
            );
            $endpoints[] = (new RouteEndpointSpec($routeEndpoint, '/eeee/'))
                ->toArray();
            $objects->next();
        }
        $this->array['endpoints'] = $endpoints;

        // xdd($this->array);
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
