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
use Chevere\Interfaces\Spec\Specs\GroupSpecInterface;
use Chevere\Interfaces\Spec\Specs\RouteSpecInterface;
use Chevere\Interfaces\Spec\Specs\RoutableSpecsInterface;

final class GroupSpec implements GroupSpecInterface
{
    use SpecsTrait;

    private RoutableSpecsInterface $routableSpecs;

    public function __construct(DirInterface $specDir, string $groupName)
    {
        $this->jsonPath = $specDir
            ->getChild("${groupName}/")
            ->path()
            ->toString() . 'routes.json';
        $this->key = $groupName;
        $this->routableSpecs = new RoutableSpecs();
    }

    public function withAddedRoutableSpec(RouteSpecInterface $routableSpec): GroupSpecInterface
    {
        $new = clone $this;
        $new->routableSpecs = $new->routableSpecs->withPut($routableSpec);

        return $new;
    }

    public function toArray(): array
    {
        $routes = [];
        foreach ($this->routableSpecs->getGenerator() as $key => $routableSpec) {
            $routes[$key] = $routableSpec->toArray();
        }

        return [
            'name' => $this->key,
            'spec' => $this->jsonPath,
            'routes' => $routes,
        ];
    }
}
