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
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Router\Routeable;
use Chevere\Components\Router\RouteableObjectsRead;
use SplObjectStorage;

final class GroupSpec implements ToArrayInterface
{
    private SplObjectStorage $objects;

    private $array = [];

    public function __construct(
        string $specPath,
        RouteableObjectsRead $objects
    ) {
        $this->objects = new SplObjectStorage;
        $this->array = [
            'name' => basename($specPath),
            'spec' => $specPath . 'routes.json',
            'routes' => [],
        ];
        $objects->rewind();
        while ($objects->valid()) {
            $routeableSpec = new RouteableSpec(
                $specPath . $objects->current()->route()->name()->toString() . '/',
                $objects->current()
            );
            $this->objects->attach($routeableSpec);
            $this->array['routes'][] = $routeableSpec->toArray();
            $objects->next();
        }
    }

    public function toArray(): array
    {
        return $this->array;
    }

    // public function objects():
}
