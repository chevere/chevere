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

use Chevere\Components\Spec\Interfaces\SpecInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\Specs\RouteableSpecs;
use Chevere\Components\Spec\Specs\Traits\SpecsTrait;
use function DeepCopy\deep_copy;

final class GroupSpec implements SpecInterface
{
    use SpecsTrait;

    private RouteableSpecs $routeableSpecs;

    /**
     * @var SpecPathInterface $specRoot /spec
     */
    public function __construct(SpecPathInterface $specRoot, string $groupName)
    {
        $this->jsonPath = $specRoot->getChild("$groupName/routes.json")->pub();
        $this->key = $groupName;
        $this->routeableSpecs = new RouteableSpecs;
    }

    public function withAddedRouteableSpec(RouteableSpec $routeableSpec): GroupSpec
    {
        $new = clone $this;
        $new->routeableSpecs = $this->routeableSpecs->withPut($routeableSpec);

        return $new;
    }

    public function toArray(): array
    {
        $routes = [];
        /**
         * @var string $key
         * @var RouteableSpec $routeableSpec
         */
        foreach ($this->routeableSpecs->map() as $key => $routeableSpec) {
            $routes[$key] = $routeableSpec->toArray();
        }

        return [
            'name' => $this->key,
            'spec' => $this->jsonPath,
            'routes' => $routes,
        ];
    }
}
